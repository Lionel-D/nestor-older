<?php

namespace App\Tests\Controller;

use App\Tests\AppTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

        $testSection = $this->getLastAddedSection();

        $crawler = $this->successfullyLoadEditPage($testSection['id']);
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

        $testSection = $this->getLastAddedSection();

        $crawler = $this->successfullyLoadEditPage($testSection['id']);
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

    public function testShow(): void
    {
        $this->assertLoggedAsUser();

        $testSection = $this->getLastAddedSection();

        $this->kernelBrowser->request('GET', '/app/section/'.$testSection['id']);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Section details');
        $this->assertSelectorTextContains('dl', 'Some Section');
        $this->assertSelectorTextContains('dl', 'Some description for this section');
    }

    public function testEditSuccessfulAddImage(): void
    {
        $this->assertLoggedAsUser();

        $testSection = $this->getLastAddedSection();

        $crawler = $this->successfullyLoadEditPage($testSection['id']);
        $testImage = new UploadedFile(__DIR__.'/../vegetables.jpg', 'vegetables.jpg', 'image/jpeg');
        $formData = [
            'name' => 'Some Section',
            'description' => 'Some description for this section',
            'image' => $testImage,
        ];

        $this->fillAndSubmitSectionForm($crawler, $formData);

        $this->assertResponseRedirects('/app/section/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Section updated !');
        $this->assertSelectorTextContains('table', 'Some Section');
        $this->assertSelectorTextContains('table', 'Some description for this section');

        $testSection = $this->getLastAddedSection();

        $this->assertStringContainsString('/uploads/section/tiny_vegetables', $testSection['cols'][0]['img']);
        $this->assertFileExists(__DIR__.'/../../public/'.$testSection['cols'][0]['img']);
    }

    public function testEditSuccessfulRemoveImage(): void
    {
        $this->assertLoggedAsUser();

        $testSection = $this->getLastAddedSection();
        $crawler = $this->successfullyLoadEditPage($testSection['id']);

        $imgSrc = $crawler->filter('.img-thumbnail')->first()->children()->eq(1)->attr('src');

        $form = $crawler->selectButton('section_'.$testSection['id'].'_img_submit')->form();

        $this->kernelBrowser->submit($form);

        $this->assertResponseRedirects('/app/section/'.$testSection['id'].'/edit');
        $crawler = $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Image deleted !');

        $newImgSrc = $crawler->filter('.img-thumbnail')->first()->children()->first()->attr('src');

        $this->assertSame('/build/images/section-default-thumbnail.jpg', $newImgSrc);
        $this->assertFileNotExists(__DIR__.'/../../public/'.$imgSrc);
    }

    public function testDeleteSuccessful(): void
    {
        $this->assertLoggedAsUser();

        $testSection = $this->getLastAddedSection();
        $crawler = $this->kernelBrowser->request('GET', '/app/section/');
        $form = $crawler->selectButton('section_'.$testSection['id'].'_submit')->form();

        $this->kernelBrowser->submit($form);

        $this->assertResponseRedirects('/app/section/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Section deleted !');
    }

    public function testNewSuccessfulWIthImage(): void
    {
        $this->assertLoggedAsUser();

        $crawler = $this->successfullyLoadCreatePage();
        $testImage = new UploadedFile(__DIR__.'/../vegetables.jpg', 'vegetables.jpg', 'image/jpeg');
        $formData = [
            'name' => 'Some Other Section',
            'description' => '',
            'image' => $testImage,
        ];

        $this->fillAndSubmitSectionForm($crawler, $formData);

        $this->assertResponseRedirects('/app/section/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Section added !');
        $this->assertSelectorTextContains('table', 'Some Other Section');

        $testSection = $this->getLastAddedSection();

        $this->assertStringContainsString('/uploads/section/tiny_vegetables', $testSection['cols'][0]['img']);
        $this->assertFileExists(__DIR__.'/../../public/'.$testSection['cols'][0]['img']);
    }

    public function testDeleteSuccessfulWithImage(): void
    {
        $this->assertLoggedAsUser();

        $testSection = $this->getLastAddedSection();
        $crawler = $this->kernelBrowser->request('GET', '/app/section/');
        $form = $crawler->selectButton('section_'.$testSection['id'].'_submit')->form();

        $this->kernelBrowser->submit($form);

        $this->assertResponseRedirects('/app/section/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Section deleted !');
        $this->assertFileNotExists(__DIR__.'/../../public/'.$testSection['cols'][0]['img']);
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
            /** @var FileFormField $fileFormField */
            $fileFormField = $form['section[image]'];
            $fileFormField->upload($formData['image']);
        }

        $this->kernelBrowser->submit($form);
    }

    /**
     * @return mixed[]
     */
    private function getLastAddedSection(): array
    {
        $crawler = $this->kernelBrowser->request('GET', '/app/section/');

        $sectionList = $crawler->filter('table')->first()->filter('tr')->each(
            fn ($tr, $i) => [
                'id' => $tr->attr('id'),
                'cols' => $tr->filter('td')->each(fn ($td, $i) => [
                    'img' => $td->children()->attr('src'),
                    'text' => $td->text(),
                ]),
            ]
        );

        return array_pop($sectionList);
    }
}
