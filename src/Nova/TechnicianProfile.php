<?php

namespace Haxibiao\Store\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Resource;
use LifeOnScreen\SortRelations\SortRelations;

class TechnicianProfile extends Resource
{

    use SortRelations;

    public static $displayInNavigation = false;

    public static $model  = \App\TechnicianProfile::class;
    public static $title  = 'id';
    public static $search = [
        'id',
    ];

    public static function label()
    {
        return '技师信息';
    }

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

            Number::make('编号', 'number'),
            Number::make('评分', 'rating')->default(0),
            Select::make('所属商铺', 'store_id')->rules('required')->options(
                \App\Store::where("user_id", getUser()->id)->pluck("name", "id")
            )->displayUsingLabels(),

            Number::make('服务次数', 'serve_count')->default(0),

            Select::make('状态', 'status')->options(\App\TechnicianProfile::getStatus())->default(\App\TechnicianProfile::FREE_STATUS)->displayUsingLabels(),

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
