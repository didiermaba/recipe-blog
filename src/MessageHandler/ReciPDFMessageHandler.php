<?php

namespace App\MessageHandler;

use App\Message\ReciPDFMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
final class ReciPDFMessageHandler{

    public function __construct(
        #[Autowire('%kernel.project_dir%/public/pdfs')]
        private readonly string $path,
        #[Autowire('%app.gotenberg_endpoint')]
        private readonly string $gotenbergEndPoint,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function __invoke(ReciPDFMessage $message): void
    {
      $process = new Process([
        'curl',
        '--request',
        'POST',
        sprintf("%s/forms/chromium/convert/url", $this->gotenbergEndPoint),
        '--form',
        'url='. $this->urlGenerator->generate('recipes.show', ['id' => $message->id], UrlGeneratorInterface::ABSOLUTE_URL),
        '-o',
        $this->path . '/' . $message->id . '.pdf'
      ]);
      $process->run();
      if (!$process->isSuccessful()) {
       throw new ProcessFailedException($process);
      }

}
}