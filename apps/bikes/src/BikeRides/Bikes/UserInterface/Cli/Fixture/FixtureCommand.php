<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Cli\Fixture;

use BikeRides\Foundation\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class FixtureCommand extends Command
{
    private readonly HttpClientInterface $client;
    private ResponseInterface $lastResponse;
    private OutputInterface $output;

    public function __construct(
        private readonly string $bikesApiUrl,
        HttpClientInterface $client,
    ) {
        parent::__construct();

        $this->client = $client->withOptions(['base_uri' => $bikesApiUrl]);
    }

    final public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        return $this->doExecute($input, $output);
    }

    abstract public function doExecute(InputInterface $input, OutputInterface $output): int;

    /** @return array<mixed, mixed> */
    protected function getJson(string $url): array
    {
        $this->output->write("<options=bold>GET</> <fg=blue>{$this->getPathFromUrl($url)}</> ");

        $this->lastResponse = $this->client->request('GET', $url);

        $this->printStatusCode($this->lastResponse->getStatusCode());
        $this->printJsonIfVerbose($this->lastResponse->getContent());

        return $this->lastResponse->toArray();
    }

    /**
     * @param array<mixed, mixed> $body
     *
     * @return ?array<mixed, mixed>
     */
    protected function postJson(string $url, array $body = []): ?array
    {
        $this->output->write("<options=bold>POST</> <fg=blue>{$this->getPathFromUrl($url)}</> ");

        $this->lastResponse = $this->client->request('POST', $url, ['json' => $body]);

        $this->printStatusCode($this->lastResponse->getStatusCode());
        $this->printJsonIfVerbose($this->lastResponse->getContent());

        return '' !== $this->lastResponse->getContent() ? $this->lastResponse->toArray() : null;
    }

    protected function parseResponseLinkUrl(): string
    {
        $link = $this->lastResponse->getHeaders()['link'][0] ?? '';

        \preg_match('/<(.+)>; rel="[^\"]+"/', $link, $matches);

        return $matches[1];
    }

    private function getPathFromUrl(string $url): string
    {
        if (\str_starts_with($url, $this->bikesApiUrl)) {
            return \mb_substr($url, \mb_strlen($this->bikesApiUrl));
        }

        return $url;
    }

    private function printStatusCode(int $statusCode): void
    {
        $style = 400 <= $statusCode ? 'error' : 'info';
        $this->output->writeln("<{$style}>{$statusCode}</>");
    }

    /** @param array<mixed, mixed>|string $data */
    private function printJsonIfVerbose(array|string $data): void
    {
        if (!$this->output->isVerbose()) {
            return;
        }

        if (empty($data)) {
            $this->output->writeln('""');

            return;
        }

        if (\is_string($data)) {
            $data = Json::decode($data);
        }

        $this->output->writeln(Json::encode($data, \JSON_PRETTY_PRINT));
    }
}
