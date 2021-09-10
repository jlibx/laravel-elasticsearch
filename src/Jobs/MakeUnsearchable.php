<?php
declare(strict_types=1);

namespace Kabunx\Elastic\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Kabunx\Elastic\Contracts\SearchableInterface;

class MakeUnsearchable implements ShouldQueue, ShouldBeUnique
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected SearchableInterface $model;

    public function __construct(SearchableInterface $model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        $this->model->newEsBuilder()->delete(
            Collection::make([$this->model])
        );
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return (string)$this->model->getEsId();
    }
}
