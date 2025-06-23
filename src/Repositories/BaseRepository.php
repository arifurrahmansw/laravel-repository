<?php
namespace ArifurRahmanSw\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function all() { return $this->model->all(); }

    public function find($id) { return $this->model->find($id); }

    public function create(array $data) { return $this->model->create($data); }

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

    // Response Utilities
    public function formatResponse(
        bool $status,
        string $message,
        string $redirect_to,
        $data = null,
        string $flash_type = ''
    ): object {
        return (object) [
            'status'         => $status,
            'message'        => $message,
            'description'    => $message,
            'data'           => $data,
            'id'             => (!is_array($data) && $data !== '') ? $data : 0,
            'redirect_to'    => $redirect_to,
            'redirect_class' => $flash_type ?: ($status ? 'success' : 'danger'),
        ];
    }

    public function successResponse(
        int $code,
        string $message,
        $data = null,
        int $status = 1,
        string $description = ''
    ): object {
        return (object) [
            'status'      => $status,
            'success'     => true,
            'code'        => $code,
            'message'     => $message,
            'description' => $description,
            'data'        => $data,
            'errors'      => null,
        ];
    }

    protected function jsonResponse(
        ?string $message = null,
        array|object $data = [],
        int $statusCode = 200,
        array $headers = []
    ): JsonResponse {
        $content = [];

        if ($message) {
            $content['message'] = $message;
        }

        if (!empty($data)) {
            $content['data'] = $data;
        }

        return response()->json($content, $statusCode, $headers, JSON_PRESERVE_ZERO_FRACTION);
    }
}
