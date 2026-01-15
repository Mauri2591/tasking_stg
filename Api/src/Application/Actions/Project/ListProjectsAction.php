<?php
declare(strict_types=1);

namespace App\Application\Actions\Project;

use Psr\Http\Message\ResponseInterface as Response;

class ListProjectsAction extends ProjectAction
{
    protected function action(): Response
    {
        $projects = $this->projectRepository->findAll();
        return $this->respondWithData($projects);
    }
}
