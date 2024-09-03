<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ScriptController extends AbstractController
{
    #[Route('/run-script', name: 'app_run_script')]
    public function runScript(): Response
    {
        // Définir le chemin du script Python
        $scriptPath = $this->getParameter('kernel.project_dir') . '/public/python/test.py';

        // Créer un nouveau processus pour exécuter le script Python
        $process = new Process(['python3', $scriptPath, 'Bonjour']); // Le dernier argument est un argument exemple passé au script

        // Exécuter le processus
        $process->run();

        // Vérifier si le processus s'est exécuté avec succès
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Obtenir et retourner la sortie du script Python
        $output = $process->getOutput();

        return new Response("<pre>$output</pre>");
    }
}
