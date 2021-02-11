<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class ExchangeConfig extends Resource
{
    public static $model = \App\ExchangeConfig::class;
    public static function label()
    {
        return "兑换配置";
    }
    public static $group = '交易中心';
    public static $title = 'name';

    public static $search = [
        'id', 'name', 'value',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('金额', 'num'),
            Text::make('描述', 'name'),
            Text::make('兑换值', 'value'),
            Select::make('状态', 'status')->options([
                "1" => "启用",
                "0" => "禁用",
            ])->displayUsingLabels(),
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
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
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
        return [];
    }
}
