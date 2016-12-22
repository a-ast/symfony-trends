<?php

namespace spec\AppBundle\Builder;

use AppBundle\Builder\ContributorBuilder;
use AppBundle\Client\ApiFacade;
use AppBundle\Entity\Contributor;
use AppBundle\Factory\ContributorFactory;
use AppBundle\Model\GithubCommit;
use AppBundle\Repository\ContributorRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ContributorBuilder
 */
class ContributorBuilderSpec extends ObjectBehavior
{
    function let(ContributorRepository $repository, ContributorFactory $factory, ApiFacade $apiFacade)
    {
        $this->beConstructedWith($repository, $factory, $apiFacade);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContributorBuilder::class);
    }

    function it_should_create_new_contributor(ContributorRepository $repository, ContributorFactory $factory, ApiFacade $apiFacade)
    {
        $this->initializeMocks($repository, $factory, $apiFacade);

        $factory
            ->createFromEmail('frodo.baggins.ext@shire')
            ->shouldBeCalled()
            ->willReturn($this->createContrubutorFromEmail('frodo.baggins.ext@shire'));

        $contributor = $this->buildFromGithubCommit($this->getCommit());

        $contributor->getGithubId()->shouldBe(300);
        $contributor->getGithubLogin()->shouldBe('frodo.ext');
        $contributor->getName()->shouldBe('Frodo ext');
        $contributor->getEmail()->shouldBe('frodo.baggins.ext@shire');
        $contributor->getCountry()->shouldBe('');
        $contributor->getGithubLocation()->shouldBe('');
        $contributor->getGitNames()->shouldBe([]);
        $contributor->getGitEmails()->shouldBe([]);
    }

    function it_should_update_names_of_existing_contributor_found_by_id(ContributorRepository $repository, ContributorFactory $factory, ApiFacade $apiFacade)
    {
        $this->initializeMocks($repository, $factory, $apiFacade);

        $repository
            ->findByGithubId(300)
            ->willReturn($this->createContributorFromArray(['email' => 'frodo.baggins.ext@shire', 'name' => 'Frodo', 'github_login' => 'frodo']));

        $contributor = $this->buildFromGithubCommit($this->getCommit());
        $contributor->getName()->shouldBe('Frodo');
        $contributor->getGithubLogin()->shouldBe('frodo');
        $contributor->getGitNames()->shouldBe(['Frodo ext', 'frodo.ext']);
    }

    function it_should_update_emails_of_existing_contributor_found_by_id(ContributorRepository $repository, ContributorFactory $factory, ApiFacade $apiFacade)
    {
        $this->initializeMocks($repository, $factory, $apiFacade);

        $repository
            ->findByGithubId(300)
            ->willReturn($this->createContributorFromArray(['email' => 'frodo.baggins.ext2@shire']));

        $contributor = $this->buildFromGithubCommit($this->getCommit());
        $contributor->getEmail()->shouldBe('frodo.baggins.ext2@shire');
        $contributor->getGitEmails()->shouldBe(['frodo.baggins.ext@shire']);
    }

    function it_should_update_names_of_existing_contributor_found_by_email(ContributorRepository $repository, ContributorFactory $factory, ApiFacade $apiFacade)
    {
        $this->initializeMocks($repository, $factory, $apiFacade);

        $repository
            ->findByEmails(['frodo.baggins.ext@shire'])
            ->willReturn($this->createContributorFromArray([
                'email' => 'frodo.baggins.int@shire',
                'name' => 'Frodo.int'
            ]));

        $contributor = $this->buildFromGithubCommit($this->getCommit());
        $contributor->getName()->shouldBe('Frodo.int');
        $contributor->getEmail()->shouldBe('frodo.baggins.int@shire');
        $contributor->getGitNames()->shouldBe(['Frodo ext']);
        $contributor->getGitEmails()->shouldBe(['frodo.baggins.ext@shire']);
    }

    /**
     * @return GithubCommit
     */
    private function getCommit()
    {
        $commitData = [
            'sha' => 'hash-frodo',
            'commit' => [
                'author' => [ 'id' => 300, 'name' => 'Frodo ext', 'email' => 'frodo.baggins.ext@shire', 'date' => '2016-11-22T00:13:33Z',],
                'message' => 'Added thoughts about my future way',
            ],
            'author' => [ 'id' => 300, 'login' => 'frodo.ext'],
        ];

        return new GithubCommit($commitData);
    }

    /**
     * @return Contributor
     */
    private function createContrubutorFromEmail($email)
    {
        $factory = new ContributorFactory();
        $contributor = $factory->createFromEmail($email);

        return $contributor;
    }

    private function createContributorFromArray(array $data)
    {
        $factory = new ContributorFactory();
        $contributor = $factory->createFromArray($data);

        return $contributor;
    }

    /**
     * @param ContributorRepository $repository
     * @param ContributorFactory $factory
     * @param ApiFacade $apiFacade
     */
    private function initializeMocks(
        ContributorRepository $repository,
        ContributorFactory $factory,
        ApiFacade $apiFacade
    ) {
        $repository
            ->findByGithubId(300)
            ->willReturn(null);

        $repository
            ->findByEmails(Argument::type('array'))
            ->willReturn(null);

        $repository
            ->saveContributor(Argument::type(Contributor::class))
            ->shouldBeCalled();

        $apiFacade
            ->getGithubUserWithLocation('frodo.ext')
            ->willReturn(null);

        $factory
            ->createFromEmail('frodo.baggins.ext@shire')
            ->shouldNotBeCalled();
    }

}
