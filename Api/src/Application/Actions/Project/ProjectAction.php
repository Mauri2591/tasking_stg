<?php
declare(strict_types=1);

namespace App\Application\Actions\Project;

use App\Application\Actions\Action;
use App\Domain\Project\ProjectRepository;
use Psr\Log\LoggerInterface;

abstract class ProjectAction extends Action
{
    protected ProjectRepository $projectRepository;

    public function __construct(LoggerInterface $logger, ProjectRepository $projectRepository)
    {
        parent::__construct($logger);
        $this->projectRepository = $projectRepository;
    }
}
