<?php

namespace Haxibiao\Store\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class Refund extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\\Refund';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'content';

    public static $group = '电商系统';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'content',
    ];

    public static function label()
    {
        return "退款";
    }

    public static function singularLabel()
    {
        return "退款";
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
            HasOne::make('用户', 'user', User::class),
            HasOne::make('订单', 'order', Order::class),
            Text::make('内容', 'content'),
            Image::make('图片1', 'images')
                ->thumbnail(function () {
                    return $this->getImageItemUrl(0);
                })->preview(function () {
                return $this->getImageItemUrl(0);
            })->disableDownload(),
            Image::make('图片2', 'images')
                ->thumbnail(function () {
                    return $this->getImageItemUrl(1);
                })->preview(function () {
                return $this->getImageItemUrl(1);
            })->disableDownload(),
            Image::make('图片3', 'images')
                ->thumbnail(function () {
                    return $this->getImageItemUrl(2);
                })->preview(function () {
                return $this->getImageItemUrl(2);
            })->disableDownload(),
            Select::make('类型', 'status')->options([
                0 => '待处理',
                1 => '驳回申请',
                2 => '通过申请',
                3 => '已处理',
            ])->displayUsingLabels(),

            DateTime::make('反馈时间', 'created_at'),
            // Text::make('创建时间', function () {
            //     return time_ago($this->created_at);
            // }),
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
        return [
            // new \App\Nova\Actions\Feedback\RefundStatus,
        ];
    }
}
