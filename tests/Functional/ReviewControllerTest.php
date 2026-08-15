<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ReviewControllerTest extends WebTestCase
{
    private const SUBMIT_BUTTON = 'Vélemény beküldése';

    private const VALID_SUBMISSION = [
        'review[companyName]' => 'Teszt Kft.',
        'review[rating]' => '5',
        'review[reviewText]' => 'Pontosak, felkészültek, végig érthetően kommunikáltak.',
        'review[authorEmail]' => 'teszt@example.com',
    ];

    public function testAValidSubmission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/review/new');

        $client->submitForm(self::SUBMIT_BUTTON, self::VALID_SUBMISSION);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-success', 'Köszönjük a véleményed!');
        $this->assertSelectorTextContains('h1', 'Teszt Kft.');

        $review = $this->getRepository()->findOneBy(['companyName' => 'Teszt Kft.']);

        $this->assertNotNull($review);
        $this->assertSame(5, $review->getRating());
        $this->assertSame('teszt@example.com', $review->getAuthorEmail());
    }

    public function testAnInvalidEmailAddressIsRejected(): void
    {
        $client = static::createClient();
        $client->request('GET', '/review/new');

        $client->submitForm(self::SUBMIT_BUTTON, [
            ...self::VALID_SUBMISSION,
            'review[authorEmail]' => 'nem-egy-email-cim',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSelectorExists('.invalid-feedback');
        $this->assertNull($this->getRepository()->findOneBy(['companyName' => 'Teszt Kft.']));
    }

    public function testASubmissionThatFillsInTheHoneypotIsDiscarded(): void
    {
        $client = static::createClient();
        $client->request('GET', '/review/new');

        $client->submitForm(self::SUBMIT_BUTTON, [
            ...self::VALID_SUBMISSION,
            'review[website]' => 'http://spam.example',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertNull($this->getRepository()->findOneBy(['companyName' => 'Aurora Consulting Kft.']));
    }

    private function getRepository(): ReviewRepository
    {
        return self::getContainer()->get(ReviewRepository::class);
    }
}
