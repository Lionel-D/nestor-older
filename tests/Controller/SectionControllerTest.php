<?php

namespace App\Tests\Controller;

use App\Tests\AppTestCase;
use Symfony\Component\DomCrawler\Crawler;

final class SectionControllerTest extends AppTestCase
{
    public function testIndex(): void
    {
        $this->assertLoggedAsUser();

        $this->kernelBrowser->request('GET', '/app/section/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Sections list');
    }

    public function testNewFailedNoName(): void
    {
        $this->assertLoggedAsUser();

        $crawler = $this->successfullyLoadCreatePage();
        $formData = [
            'name' => '',
            'description' => 'Some description for this section',
        ];

        $this->fillAndSubmitSectionForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'Please choose a name');
    }

    public function testNewSuccessfulNoDescription(): void
    {
        $this->assertLoggedAsUser();

        $crawler = $this->successfullyLoadCreatePage();
        $formData = [
            'name' => 'Some Section',
            'description' => '',
        ];

        $this->fillAndSubmitSectionForm($crawler, $formData);

        $this->assertResponseRedirects('/app/section/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Section added !');
        $this->assertSelectorTextContains('table', 'Some Section');
    }

    public function testEditFailedNoName(): void
    {
        $this->assertLoggedAsUser();

        $testSectionId = $this->getLastAddedSectionId();

        $crawler = $this->successfullyLoadEditPage($testSectionId);
        $formData = [
            'name' => '',
            'description' => 'Some description for this section',
        ];

        $this->fillAndSubmitSectionForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'Please choose a name');
    }

    public function testEditSuccessfulAddDescription(): void
    {
        $this->assertLoggedAsUser();

        $testSectionId = $this->getLastAddedSectionId();

        $crawler = $this->successfullyLoadEditPage($testSectionId);
        $formData = [
            'name' => 'Some Section',
            'description' => 'Some description for this section',
        ];

        $this->fillAndSubmitSectionForm($crawler, $formData);

        $this->assertResponseRedirects('/app/section/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Section updated !');
        $this->assertSelectorTextContains('table', 'Some Section');
        $this->assertSelectorTextContains('table', 'Some description for this section');
    }

    public function testDeleteSuccessful(): void
    {
        $this->assertLoggedAsUser();

        $testSectionId = $this->getLastAddedSectionId();
        $crawler = $this->kernelBrowser->request('GET', '/app/section/');
        $form = $crawler->selectButton('section_'.$testSectionId.'_submit')->form();

        $this->kernelBrowser->submit($form);

        $this->assertResponseRedirects('/app/section/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Section deleted !');
    }

    private function successfullyLoadCreatePage(): Crawler
    {
        $crawler = $this->kernelBrowser->request('GET', '/app/section/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create section');

        return $crawler;
    }

    private function successfullyLoadEditPage(string $id): Crawler
    {
        $crawler = $this->kernelBrowser->request('GET', '/app/section/'.$id.'/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit section');

        return $crawler;
    }

    /**
     * @param mixed[] $formData
     */
    private function fillAndSubmitSectionForm(Crawler $crawler, array $formData): void
    {
        $form = $crawler->selectButton('section_submit')->form();

        $form['section[name]'] = $formData['name'];
        $form['section[description]'] = $formData['description'];

        if (isset($formData['image'])) {
            $form['section[image]'] = $formData['image'];
        }

        $this->kernelBrowser->submit($form);
    }

    private function getLastAddedSectionId(): string
    {
        $crawler = $this->kernelBrowser->request('GET', '/app/section/');

        $sectionList = $crawler->filter('table')->first()->filter('tr')->each(
            function ($tr, $i) {
                return [
                    'id' => $tr->attr('id'),
                    'cols' => $tr->filter('td')->each(function ($td, $i) {
                        return $td->text();
                    }),
                ];
            }
        );

        $lastAdded = array_pop($sectionList);

        return $lastAdded['id'];
    }
}