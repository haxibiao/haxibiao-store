<?php

namespace Haxibiao\Store\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;

class Profile extends Resource
{
    public static $model  = \App\UserProfile::class;
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
            Number::make('年龄', 'age'),
            Textarea::make('介绍', 'introduction'),
            Select::make('性别', 'gender')->default(\App\User::FEMALE_GENDER)->options(\App\User::getGenders())->displayUsingLabels(),
            Text::make('QQ', 'qq'),
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
