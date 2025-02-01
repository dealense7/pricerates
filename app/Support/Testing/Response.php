<?php

declare(strict_types=1);

namespace App\Support\Testing;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

use function json_decode;

/**
 * @mixin \Illuminate\Testing\TestResponse
 */
class Response extends TestResponse
{
    protected static array $successResponseStructure = [];
    protected static array $errorResponseStructure = [];
    protected static array $pagerMetaStructure = [
        'total',
        'count',
        'perPage',
        'currentPage',
        'totalPages',
        'links',
    ];

    public static function setSuccessResponseStructure(array $structure): void
    {
        self::$successResponseStructure = $structure;
    }

    public static function setErrorResponseStructure(array $structure): void
    {
        self::$errorResponseStructure = $structure;
    }

    public function assertJsonDataCount(int $count): self
    {
        $response = (array) $this->getDecodedContent();

        PHPUnit::assertCount($count, $response['data'] ?? []);

        return $this;
    }

    public function assertJsonDataPagination(array $data): self
    {
        $response = $this->getDecodedContent();

        PHPUnit::assertEquals($data['page'], $response['meta']['pagination']['currentPage']);
        PHPUnit::assertEquals($data['perPage'], $response['meta']['pagination']['perPage']);
        PHPUnit::assertEquals($data['count'], $response['meta']['pagination']['count']);
        PHPUnit::assertEquals($data['total'], $response['meta']['pagination']['total']);

        return $this;
    }

    public function assertJsonDataCollectionStructure(array $data, bool $includePagerMeta = true): self
    {
        $struct         = self::$successResponseStructure;
        $struct['data'] = [$data];

        if ($includePagerMeta) {
            $struct['meta'] = [
                'pagination' => self::$pagerMetaStructure,
            ];
        }

        $this->assertJsonStructure($struct);

        return $this;
    }

    public function assertJsonDataItemStructure(array $data): self
    {
        $struct = ['data' => $data];

        $this->assertJsonStructure($struct);

        return $this;
    }

    public function assertJsonErrorStructure(): self
    {
        $this->assertJsonStructure(self::$errorResponseStructure);

        return $this;
    }

    public function assertJsonSuccessStructure(string $message = 'ok'): self
    {
        $this->assertJsonStructure(self::$successResponseStructure);
        $this->assertJson(['message' => 'ok']);

        return $this;
    }

    public function getDecodedContent(): array
    {
        $content = $this->getContent();

        return json_decode($content, true);
    }

    public function assertForbidden(): Response
    {
        parent::assertForbidden();

        //$this->assertJsonErrorStructure();
        //$this->assertJson(['message' => 'This action is unauthorized.']);

        return $this;
    }

    public function assertUnauthorized(): Response
    {
        parent::assertUnauthorized();

        //$this->assertJsonErrorStructure();
        $this->assertJson(['message' => 'Unauthenticated.']);

        return $this;
    }

    public function assertAuthenticationFailed(): Response
    {
        parent::assertUnauthorized();

        $this->assertExactJson([
            'error'             => 'invalid_client',
            'error_description' => 'Client authentication failed',
            'message'           => 'Client authentication failed',
        ]);

        return $this;
    }

    public function assertInvalidGrantOrToken(): Response
    {
        $this->assertStatus(HttpResponse::HTTP_BAD_REQUEST);

        $this->assertJsonStructure([
            'error',
            'message',
            'error_description',
            'hint',
        ]);

        return $this;
    }

    public function assertNotFound(): Response
    {
        parent::assertNotFound();

//        $this->assertJson(['message' => __('app.item_not_found')]);

        return $this;
    }

    public function assertIsInvalidItem(): Response
    {
        Assert::assertTrue(
            $this->isInvalidData(),
            'Response status code [' . $this->getStatusCode() . '] is not a invalid data status code.',
        );

        return $this;
    }

    public function assertInvalidData(): Response
    {
        Assert::assertTrue(
            $this->isInvalidData(),
            'Response status code [' . $this->getStatusCode() . '] is not a invalid data status code.',
        );
        $this->assertJsonErrorStructure();

        return $this;
    }

    public function isInvalidData(): bool
    {
        return $this->getStatusCode() === 422;
    }

    public function assertOk(): Response
    {
        parent::assertOk();

//        $this->assertJsonSuccessStructure();

        return $this;
    }

    public function assertCreated(): Response
    {
        parent::assertCreated();

        $this->assertJsonSuccessStructure();

        return $this;
    }
}
