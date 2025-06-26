<?php
namespace ArifurRahmanSw\Repository;
use ArifurRahmanSw\Repository\Contracts\BaseRepositoryInterface;
use Illuminate\Http\JsonResponse;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $model = $this->find($id);
        return $model ? $model->update($data) : false;
    }

    public function delete($id)
    {
        $model = $this->find($id);
        return $model ? $model->delete() : false;
    }

    protected function jsonResponse(?string $message = null, array|object $data = [], int $statusCode = 200, array $headers = []): JsonResponse
    {
        $content = [];
        if ($message !== null) {
            $content['message'] = $message;
        }
        if (!empty($data)) {
            $content['data'] = $data;
        }
        return response()->json($content, $statusCode, $headers, JSON_PRESERVE_ZERO_FRACTION);
    }
}
