<?php

namespace Haxibiao\Store\Nova;

use App\Role;
use Haxibiao\Breeze\Nova\Actions\User\AddMasterAccount;
use Haxibiao\Breeze\Nova\Actions\User\UpdateUserStatus;
use Haxibiao\Breeze\Nova\Filters\User\UserRoleID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use LifeOnScreen\SortRelations\SortRelations;

class User extends Resource
{

    use SortRelations;

    public static $model  = \App\User::class;
    public static $title  = 'name';
    public static $search = [
        'id', 'name', 'account',
    ];

    //关联信息查询
    public static $sortRelations = [
        'profile'           => ['profile.age'],
        'profile'           => ['profile.gender'],
        'technicianProfile' => ['technicianProfile.rating'],
        'technicianProfile' => ['technicianProfile.serve_count'],
        'technicianProfile' => ['technicianProfile.status'],
    ];

    //只查技师列表
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('role_id', Role::TECHNICIAN_USER);
    }

    //创建技师
    // public static function fill(NovaRequest $request, $model)
    // {

    //     $data = $request->all();
    //     if (is_null($data['api_token'])) {
    //         $request->set('api_token', str_random(60));
    //     }

    //     return static::fillFields(
    //         $request, $model,
    //         ['api_token', 'name', 'account', 'email', 'password']
    //     );
    // }

    public static $group = "电商系统";

    public static function label()
    {
        return '技师';
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

            Gravatar::make()->maxWidth(50),

            Text::make('编号', 'technicianProfile.number')->exceptOnForms(),

            Text::make('昵称', 'name')
                ->rules('required', 'max:255'),

            Text::make('api_token', 'api_token')->default(str_random(60))->showOnCreating(),

            Select::make('角色', 'role_id')->default(Role::TECHNICIAN_USER)->options([ROle::TECHNICIAN_USER => '技师'])->displayUsingLabels(),

            Text::make('手机号', 'account')
                ->rules('required', 'max:255'),

            Text::make('年龄', 'profile.age')->exceptOnForms(),

            Select::make('性别', 'profile.gender')->default(\App\User::FEMALE_GENDER)->options(\App\User::getGenders())->displayUsingLabels(),

            Text::make('评分', 'technicianProfile.rating')->exceptOnForms(),

            Text::make('服务次数', 'technicianProfile.serve_count')->exceptOnForms(),

            Select::make('状态', 'technicianProfile.status')->options(\App\TechnicianProfile::getStatus())->exceptOnForms()->displayUsingLabels(),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')->onlyOnForms(),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:6')
                ->updateRules('nullable', 'string', 'min:6')->onlyOnForms(),

            HasOne::make('技师信息', 'technicianProfile', TechnicianProfile::class, ),
            HasOne::make('用户信息', 'profile', Profile::class),

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
            new UserRoleID,
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
            new AddMasterAccount,
            new UpdateUserStatus,
        ];
    }
}
