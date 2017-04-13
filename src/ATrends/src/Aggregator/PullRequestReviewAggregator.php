<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Entity\PullRequestReview;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Entity\PullRequest as EntityPullRequest;
use Aa\ATrends\Repository\PullRequestRepository;
use Aa\ATrends\Repository\PullRequestReviewRepository;
use DateTimeInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class PullRequestReviewAggregator implements ProjectAwareAggregatorInterface
{
    use ProjectAwareTrait, ProgressNotifierAwareTrait;

    /**
     * @var GithubApiInterface
     */
    private $githubApi;

    /**
     * @var PullRequestRepository
     */
    private $pullRequestRepository;

    /**
     * @var PullRequestReviewRepository
     */
    private $reviewRepository;

    /**
     * Constructor.
     *
     * @param GithubApiInterface          $githubApi
     * @param PullRequestRepository       $repository
     * @param PullRequestReviewRepository $reviewRepository
     */
    public function __construct(GithubApiInterface $githubApi,
        PullRequestRepository $repository,
        PullRequestReviewRepository $reviewRepository)
    {
        $this->githubApi = $githubApi;
        $this->pullRequestRepository = $repository;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(OptionsInterface $options)
    {
        $count = 0;

        foreach ($this->pullRequestRepository->findAllPullRequests($this->getProject()->getId()) as $pullRequest) {
            $reviews = $this->githubApi->getPullRequestReviews($this->getProject()->getGithubPath(), $pullRequest->getNumber());

            //$this->reviewRepository->removeByPullRequestId($pullRequest->getId());

            foreach ($reviews as $apiReview) {

                $review = new PullRequestReview();

                $review
                    ->setPullRequestId($pullRequest->getId())
                    ->setGithubId($apiReview->getId())
                    ->setState($apiReview->getState())
                    ->setGithubUserId($apiReview->getUserId())
                    ->setSubmittedAt($apiReview->getSubmittedAt());

                $this->reviewRepository->persist($review);
                $count++;
            }


            if (0 === ($count % 100)) {
                $this->reviewRepository->flush();
            }

            $this->progressNotifier->advance();
        }

        $this->progressNotifier->setMessage('Flushing...');
        $this->reviewRepository->flush();

        print $count;
    }
}
