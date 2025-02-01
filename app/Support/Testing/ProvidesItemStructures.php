<?php

declare(strict_types=1);

namespace App\Support\Testing;

use Illuminate\Support\Str;
use InvalidArgumentException;

trait ProvidesItemStructures
{
    // phpcs:disable Universal.Arrays.MixedKeyedUnkeyedArray
    // phpcs:disable Universal.Arrays.MixedArrayKeyTypes

    private array $success_structure = [
        'message',
        'data',
    ];

    private array $error_structure = [
        'message',
        'errors',
    ];

    private array $access_token_structure = [
        'token_type',
        'expires_in',
        'access_token',
        'refresh_token',
    ];

    private array $language_structure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'slug',
            'createdAt',
            'updatedAt',
        ],
    ];

    private array $company_structure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'displayName',
            'createdAt',
            'updatedAt',
        ],
    ];

    private array $user_structure = [
        'type',
        'id',
        'attributes' => [
            'username',
            'firstName',
            'lastName',
            'email',
            'createdAt',
            'updatedAt',
        ],
    ];

    private array $contact_information_structure = [
        'type',
        'id',
        'attributes' => [
            'type',
            'typeToText',
            'data',
        ],
    ];

    private array $role_structure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'displayName',
            'guardName',
            'createdAt',
            'updatedAt',
        ],
    ];

    private array $permission_structure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'displayName',
            'guardName',
            'createdAt',
            'updatedAt',
        ],
    ];

    private array $user_status_structure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'createdAt',
            'updatedAt',
        ],
    ];

    public function getAccessTokenStructure(array $relations = []): array
    {
        $structure = $this->access_token_structure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    public function getUserStructure(array $relations = []): array
    {
        $structure = $this->user_structure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    public function getContactInformationStructure(array $relations = []): array
    {
        $structure = $this->contact_information_structure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    public function getRoleStructure(array $relations = []): array
    {
        $structure = $this->role_structure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    public function getPermissionStructure(array $relations = []): array
    {
        $structure = $this->permission_structure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    public function getUserStatusStructure(array $relations = []): array
    {
        $structure = $this->user_status_structure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    public function getLanguageStructure(array $relations = []): array
    {
        $structure = $this->language_structure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    public function getCompanyStructure(array $relations = []): array
    {
        $structure = $this->company_structure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    protected function getSuccessStructure(): array
    {
        return $this->success_structure;
    }

    protected function getErrorStructure(): array
    {
        return $this->error_structure;
    }

    protected function includeNestedRelations(array &$item, array $relations): void
    {
        if (empty($relations)) {
            return;
        }

        foreach ($relations as $relation) {
            $parentRelations = explode('.', $relation);
            $this->includeNestedRelation($item, $parentRelations);
        }
    }

    /**
     * Nested relation support
     */
    protected function includeNestedRelation(array &$item, array $parentRelations = []): void
    {
        $currentRelation = array_shift($parentRelations);
        /* check if we reached bottom of the relation tree, if so add new relation to the tree*/
        if (empty($parentRelations)) {
            // Set relation collection by default to false
            $isRelationCollection = false;
            if (Str::startsWith($currentRelation, '[')) {
                $currentRelation      = trim($currentRelation, '[]');
                $isRelationCollection = true;
            }

            if (Str::contains($currentRelation, ':')) {
                [$relationKey, $relationItem] = explode(':', $currentRelation);
            } else {
                $relationKey  = $currentRelation;
                $relationItem = $currentRelation;
            }

            $property = Str::snake($relationItem) . '_structure';
            if (! property_exists($this, $property)) {
                throw new InvalidArgumentException('Relation structure for ' . $relationItem . ' does not exists');
            }

            if ($isRelationCollection) {
                $item['relationships'][$relationKey]['data'][0] = $this->{strtolower($property)};
            } else {
                $item['relationships'][$relationKey]['data'] = $this->{strtolower($property)};
            }
        } else {
            // get to the bottom of the relation tree
            if (Str::startsWith($currentRelation, '[')) {
                $currentRelation = trim($currentRelation, '[]');
                $this->includeNestedRelation($item['relationships'][$currentRelation]['data'][0], $parentRelations);
            } else {
                $this->includeNestedRelation($item['relationships'][$currentRelation]['data'], $parentRelations);
            }
        }
    }

    /** @deprecated */
    protected function includeRelations(array &$item, array $relations): void
    {
        if (empty($relations)) {
            return;
        }

        foreach ($relations as $relation) {
            $this->includeRelation($item, $relation);
        }
    }

    /** @deprecated */
    protected function includeRelation(array &$item, $relations): void
    {
        if (is_array($relations)) {
            $relationKey  = key($relations);
            $relationItem = reset($relations);

            if (is_array($relationItem)) {
                $subRelations = reset($relationItem);
                $relationItem = key($relationItem);
            }
        } else {
            $relationKey  = $relations;
            $relationItem = $relations;
        }

        $property = Str::snake($relationItem) . '_structure';

        if (! property_exists($this, $property)) {
            throw new InvalidArgumentException('Relation structure for ' . $relationItem . ' does not exists');
        }

        if (is_array($relations)) {
            $item['attributes'][$relationKey]['data'][0] = $this->{strtolower($property)};

            if (! empty($subRelations)) {
                $this->includeRelations($item['attributes'][$relationKey]['data'][0], $subRelations);
            }
        } else {
            $item['attributes'][$relationItem]['data'] = $this->{strtolower($property)};

            if (! empty($subRelations)) {
                $this->includeRelations($item['attributes'][$relationKey]['data'], $subRelations);
            }
        }
    }
}
