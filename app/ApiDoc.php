<?php
/**
 * @SWG\Swagger(
 *    swagger="2.0",
 *     schemes={"http","https"},
 *     host="driving.test",
 *     basePath="/api",
 *     @SWG\Tag(name="Login", description="登录模块"),
 *     @SWG\Tag(name="Trainers", description="教练模块"),
 *     @SWG\Tag(name="Students", description="学员模块"),
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="驾校小程序 Api 文档",
 *     )
 * )
 * @SWG\Get(
 *     tags={"Students"},
 *     path="/alltrainers",
 *     summary="获取所有已认证教练列表",
 *     description="返回所有已认证教练列表。",
 *     @SWG\Response(
 *         response=200,
 *         description="所有已认证教练列表",
 *         @SWG\Schema(
 *              type="array",
 *              @SWG\Items(
 *                  @SWG\Property(
 *                      property="id",
 *                      type="integer",
 *                      description="用户ID"
 *                  ),
 *                  @SWG\Property(
 *                      property="avatar",
 *                      type="string",
 *                      description="头像"
 *                  ),
 *                  @SWG\Property(
 *                      property="phone",
 *                      type="string",
 *                      description="手机号"
 *                  ),
 *                  @SWG\Property(
 *                      property="carno",
 *                      type="string",
 *                      description="身份证号"
 *                  ),
 *                  @SWG\Property(
 *                      property="type",
 *                      type="string",
 *                      description="会员类型"
 *                  ),
 *                  @SWG\Property(
 *                      property="f_uid",
 *                      type="unsignedInteger",
 *                      description="所属教练"
 *                  ),
 *                  @SWG\Property(
 *                      property="subject",
 *                      type="unsignedInteger",
 *                      description="学员科目"
 *                  ),
 *                  @SWG\Property(
 *                      property="car_number",
 *                      type="string",
 *                      description="教练车牌号码"
 *                  )
 *              )
 *          )
 *     ),
 * )
 * @SWG\Get(
 *     tags={"Students"},
 *     path="/mytrainer",
 *     summary="获取我的证教练信息",
 *     description="返回我的证教练信息。",
 *     @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
 *     @SWG\Response(
 *         response=200,
 *         description="我的证教练信息",
 *         @SWG\Schema(
 *              type="array",
 *              @SWG\Items(
 *                  @SWG\Property(
 *                      property="id",
 *                      type="integer",
 *                      description="用户ID"
 *                  ),
 *                  @SWG\Property(
 *                      property="avatar",
 *                      type="string",
 *                      description="头像"
 *                  ),
 *                  @SWG\Property(
 *                      property="phone",
 *                      type="string",
 *                      description="手机号"
 *                  ),
 *                  @SWG\Property(
 *                      property="carno",
 *                      type="string",
 *                      description="身份证号"
 *                  ),
 *                  @SWG\Property(
 *                      property="type",
 *                      type="string",
 *                      description="会员类型"
 *                  ),
 *                  @SWG\Property(
 *                      property="f_uid",
 *                      type="unsignedInteger",
 *                      description="所属教练"
 *                  ),
 *                  @SWG\Property(
 *                      property="subject",
 *                      type="unsignedInteger",
 *                      description="学员科目"
 *                  ),
 *                  @SWG\Property(
 *                      property="car_number",
 *                      type="string",
 *                      description="教练车牌号码"
 *                  )
 *              )
 *          )
 *     ),
 * )
 */

 /**
 * @SWG\Post(
 *     tags={"Students"},
 *     path="/persons",
 *     summary="Creates a person",
 *     description="Adds a new person to the persons list.",
 *     @SWG\Parameter(
 *          name="person",
 *          in="body",
 *          required=true,
 *          description="The person to create.",
 *          @SWG\Schema(
 *              required={"username"},
 *              @SWG\Property(
 *                  property="firstName",
 *                  type="string"
 *              ),
 *              @SWG\Property(
 *                   property="lastName",
 *                   type="string"
 *              ),
 *              @SWG\Property(
 *                   property="username",
 *                   type="string"
 *              )
 *          )
 *     ),
 *     @SWG\Response(
 *          response="200",
 *          description="Persons succesfully created."
 *     ),
 *     @SWG\Response(
 *          response="400",
 *          description="Persons couldn't have been created."
 *     )
 * )
 */


