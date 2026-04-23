<?php

namespace Drupal\announcements;

use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Component\Plugin\Exception\ContextException;
use Drupal\Component\Plugin\Exception\MissingValueContextException;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Condition\ConditionAccessResolverTrait;
use Drupal\Core\Condition\ConditionManager;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Plugin\Context\ContextHandlerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Access controller for the Announcement entity.
 *
 * @see \Drupal\announcements\Entity\Announcement.
 */
class AnnouncementAccessControlHandler extends EntityAccessControlHandler implements EntityHandlerInterface {

  use ConditionAccessResolverTrait;

  /**
   * The plugin context handler.
   *
   * @var \Drupal\Core\Plugin\Context\ContextHandlerInterface
   */
  protected $contextHandler;

  /**
   * The context manager service.
   *
   * @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface
   */
  protected $contextRepository;

  /**
   * The condition manager service.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('context.handler'),
      $container->get('context.repository'),
      $container->get('plugin.manager.condition')
    );
  }

  /**
   * Constructs the announcement access control handler instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Plugin\Context\ContextHandlerInterface $context_handler
   *   The ContextHandler for applying contexts to conditions properly.
   * @param \Drupal\Core\Plugin\Context\ContextRepositoryInterface $context_repository
   *   The lazy context repository service.
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    ContextHandlerInterface $context_handler,
    ContextRepositoryInterface $context_repository,
    ConditionManager $condition_manager
  ) {
    parent::__construct($entity_type);
    $this->contextHandler = $context_handler;
    $this->contextRepository = $context_repository;
    $this->conditionManager = $condition_manager;
  }

  protected function resolveConditions($conditions, $condition_logic): AccessResultInterface {
    $access = AccessResult::allowedIf($condition_logic == 'and');

    foreach ($conditions as $condition) {

      try {
        if ($condition instanceof ContextAwarePluginInterface) {
          $contexts = $this->contextRepository->getRuntimeContexts(array_values($condition->getContextMapping()));
          $this->contextHandler->applyContextMapping($condition, $contexts);
        }
        $condition_access = AccessResult::allowedIf($condition->execute());
      }
      catch (MissingValueContextException $e) {
        $access = AccessResult::forbidden()->setCacheMaxAge(0);
      }
      catch (ContextException $e) {
        if (!$condition->isNegated()) {
          $condition_access = AccessResult::forbidden()->setCacheMaxAge(0);
        }
        else {
          $condition_access = AccessResult::neutral();
        }
      }

      $condition_access->addCacheContexts($condition->getCacheContexts());
      $condition_access->mergeCacheMaxAge($condition->getCacheMaxAge());
      $condition_access->addCacheTags($condition->getCacheTags());

      // If a condition fails and all conditions were needed, deny access.
      if ($condition_logic == 'and') {
        $access = $access->andIf($condition_access);
      }
      elseif ($condition_logic == 'or') {
        $access = $access->orIf($condition_access);
      }
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\announcements\Entity\AnnouncementInterface $entity */

    switch ($operation) {

      case 'update':

        $permission = $this->checkOwn($entity, $operation, $account);
        if (!empty($permission)) {
          return AccessResult::allowed();
        }
        return AccessResult::allowedIfHasPermission($account, 'edit announcements_announcement entities');

      case 'delete':

        $permission = $this->checkOwn($entity, $operation, $account);
        if (!empty($permission)) {
          return AccessResult::allowed();
        }
        return AccessResult::allowedIfHasPermission($account, 'delete announcements_announcement entities');

      case 'view':

        if (!$entity->isPublished()) {
          $permission = $this->checkOwn($entity, 'view unpublished', $account);
          if (!empty($permission)) {
            return AccessResult::allowedIfHasPermission($account, $permission);
          }

          return AccessResult::allowedIfHasPermission($account, 'view unpublished announcements_announcement entities');
        }

        $permission = $this->checkOwn($entity, $operation, $account);
        if (!empty($permission)) {
          return AccessResult::allowedIfHasPermission($account, $permission);
        }

        $access = AccessResult::allowedIfHasPermission($account, 'view published announcements_announcement entities');

        // Ensure that access is evaluated again when the block changes.
        $access->addCacheableDependency($entity);

        if ($access->isAllowed() && $entity->hasVisibilityConditions()) {
          $conditions = $entity->getVisibilityConditionPlugins();
          $condition_access = $this->resolveConditions($conditions, 'and');
          $access = $access->andIf($condition_access);

          if ($access->isForbidden()) {
            $reason = count($conditions) > 1
              ? "One of the block visibility conditions ('%s') denied access."
              : "The block visibility condition '%s' denied access.";
            $access->setReason($reason);
          }
        }

        return $access;

    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add announcements_announcement entities');
  }

  /**
   * Test for given 'own' permission.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param $operation
   * @param \Drupal\Core\Session\AccountInterface $account
   *
   * @return string|null
   *   The permission string indicating it's allowed.
   */
  protected function checkOwn(EntityInterface $entity, $operation, AccountInterface $account) {
    $status = $entity->isPublished();
    $uid = $entity->getOwnerId();

    $is_own = $account->isAuthenticated() && $account->id() == $uid;
    if (!$is_own) {
      return;
    }

    $bundle = $entity->bundle();

    $ops = [
      'create' => '%bundle add own %bundle entities',
      'view unpublished' => '%bundle view own unpublished %bundle entities',
      'view' => '%bundle view own entities',
      'update' => '%bundle edit own entities',
      'delete' => '%bundle delete own entities',
    ];
    $permission = strtr($ops[$operation], ['%bundle' => $bundle]);

    if ($operation === 'view unpublished') {
      if (!$status && $account->hasPermission($permission)) {
        return $permission;
      }
      else {
        return NULL;
      }
    }
    if ($account->hasPermission($permission)) {
      return $permission;
    }

    return NULL;
  }

}
