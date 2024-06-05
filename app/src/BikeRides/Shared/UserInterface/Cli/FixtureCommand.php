<?php declare(strict_types=1);

namespace App\BikeRides\Shared\UserInterface\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class FixtureCommand extends Command
{
    private readonly HttpClientInterface $client;
    private ?ResponseInterface $lastResponse;
    private OutputInterface $output;

    public function __construct(
        private readonly string $bikesApiUrl,
        HttpClientInterface $client,
    ) {
        parent::__construct();

        $this->client = $client->withOptions(['base_uri' => $bikesApiUrl]);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        return $this->doExecute($input, $output);
    }

    abstract public function doExecute(InputInterface $input, OutputInterface $output): int;

    protected function getJson(string $url): array
    {
        $this->output->write("<options=bold>GET</> <fg=blue>{$this->getPathFromUrl($url)}</> ");

        $this->lastResponse = $this->client->request('GET', $url);

        $this->printStatusCode($this->lastResponse->getStatusCode());
        $this->printJsonIfVerbose($this->lastResponse->getContent());

        return $this->lastResponse->toArray();
    }

    protected function postJson(string $url, array $body = []): void
    {
        $this->output->write("<options=bold>POST</> <fg=blue>{$this->getPathFromUrl($url)}</> ");

        $this->lastResponse = $this->client->request('POST', $url, ['json' => $body]);

        $this->printStatusCode($this->lastResponse->getStatusCode());
        $this->printJsonIfVerbose($this->lastResponse->getContent());
    }

    protected function parseResponseLinkUrl(): string
    {
        $link = $this->lastResponse?->getHeaders()['link'][0] ?? '';

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
        $style = $statusCode >= 400 ? 'error' : 'info';
        $this->output->writeln("<{$style}>{$statusCode}</>");
    }

    private function printJsonIfVerbose(string|array $data): void
    {
        if (! $this->output->isVerbose()) {
            return;
        }

        if (empty($data)) {
            $this->output->writeln('""');

            return;
        }

        if (\is_string($data)) {
            $data = \json_decode_array($data);
        }

        $this->output->writeln(\json_encode_array($data, \JSON_PRETTY_PRINT));
    }
}
