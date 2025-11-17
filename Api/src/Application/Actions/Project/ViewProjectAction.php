<?php
declare(strict_types=1);

namespace App\Application\Actions\Project;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

class ViewProjectAction extends Action
{
    protected function action(): Response
    {
        $id = (int) $this->resolveArg('id');
        $project = $this->projectRepository->findById($id);

        if (!$project) {
            return $this->respondWithData(['error' => 'Proyecto no encontrado'], 404);
        }

        return $this->respondWithData($project);
    }
}
