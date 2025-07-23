<?php
/**
 * 待办事项 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TodoRequest;
use App\Http\Resources\Todo\TodoInfoResource;
use App\Http\Resources\Todo\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TodosController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $todos = Todo::query()->whereBelongsTo($adminUser)
            ->orderBy('status')
            ->latest()
            ->get();
        TodoResource::wrap('data');
        return TodoResource::collection($todos);
    }

    /**
     * 新增
     * @param TodoRequest $request
     * @param Todo $todo
     * @return TodoInfoResource
     */
    public function store(TodoRequest $request, Todo $todo): TodoInfoResource
    {
        $adminUser = $request->user();
        $todo->fill($request->all());
        $todo->adminUser()->associate($adminUser);
        $todo->save();
        return new TodoInfoResource($todo);
    }

    /**
     * 编辑
     * @param Request $request
     * @param Todo $todo
     * @return TodoInfoResource
     */
    public function update(Request $request, Todo $todo): TodoInfoResource
    {
        $todo->fill($request->all());
        $todo->update();
        return new TodoInfoResource($todo);
    }

    /**
     * 删除
     * @param Todo $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->noContent();
    }
}
