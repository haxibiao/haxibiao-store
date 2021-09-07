<?php

namespace Haxibiao\Store\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Resource;
use LifeOnScreen\SortRelations\SortRelations;

class TechnicianRoom extends Resource
{

    use SortRelations;

    public static $model  = \App\TechnicianRoom::class;
    public static $title  = 'id';
    public static $search = [
        'id',
    ];

    public static function label()
    {
        return '技师房间';
    }

    public static $group = "电商系统";

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Code::make('客户id', 'uids')->json(JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE),
            Code::make('技师id', 'tids')->json(JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE),

            Select::make('状态', 'status')->options(\App\TechnicianRoom::getStatus())->default(\App\TechnicianRoom::FREE_STATUS)->displayUsingLabels(),

            DateTime::make('创建时间', 'created_at')
                ->hideWhenUpdating()->hideWhenCreating(),
            DateTime::make('登录时间', 'updated_at')
                ->hideWhenUpdating()->hideWhenCreating(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [

        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
        ];
    }
}
