<?php

namespace App\Command;

use App\Service\GpxProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import-gpx',
    description: 'Importa un fichero GPX y guarda los puntos en la base de datos.'
)]
class ImportGpxCommand extends Command
{
    private GpxProcessor $processor;

    public function __construct(GpxProcessor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('start-time', InputArgument::REQUIRED, 'Hora de inicio (formato Y-m-d H:i:s)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $input->getArgument('start-time'));

        if (!$startTime) {
            $output->writeln('<error>Formato de fecha inválido. Usa Y-m-d H:i:s</error>');
            return Command::FAILURE;
        }

        $gpxPath = __DIR__ . '/../../gpx/track.gpx';
        if (!file_exists($gpxPath)) {
            $output->writeln('<error>El fichero GPX no existe en ' . $gpxPath . '</error>');
            return Command::FAILURE;
        }

        $this->processor->process($gpxPath, $startTime);

        $output->writeln('<info>GPX procesado y puntos importados correctamente.</info>');
        return Command::SUCCESS;
    }
}