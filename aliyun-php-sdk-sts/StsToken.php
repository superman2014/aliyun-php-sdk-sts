<?php

namespace Superman2014\Aliyun\Sts;

use Superman2014\Aliyun\Sts\Request\V20150401\AssumeRoleRequest;
use Superman2014\Aliyun\Core\Profile\DefaultProfile;
use Superman2014\Aliyun\Core\DefaultAcsClient;
use Superman2014\Aliyun\Core\Regions\ProductDomain;
use Superman2014\Aliyun\Core\Regions\Endpoint;
use Superman2014\Aliyun\Core\Regions\EndpointProvider;

$regionIds = ['cn-hangzhou', 'cn-beijing', 'cn-qingdao', 'cn-hongkong', 'cn-shanghai', 'us-west-1', 'cn-shenzhen', 'ap-southeast-1'];
$productDomains = [
    new ProductDomain('Ecs', 'ecs.aliyuncs.com'),
    new ProductDomain('Rds', 'rds.aliyuncs.com'),
    new ProductDomain('BatchCompute', 'batchCompute.aliyuncs.com'),
    new ProductDomain('Bss', 'bss.aliyuncs.com'),
    new ProductDomain('Oms', 'oms.aliyuncs.com'),
    new ProductDomain('Slb', 'slb.aliyuncs.com'),
    new ProductDomain('Oss', 'oss-cn-hangzhou.aliyuncs.com'),
    new ProductDomain('OssAdmin', 'oss-admin.aliyuncs.com'),
    new ProductDomain('Sts', 'sts.aliyuncs.com'),
    new ProductDomain('Yundun', 'yundun-cn-hangzhou.aliyuncs.com'),
    new ProductDomain('Risk', 'risk-cn-hangzhou.aliyuncs.com'),
    new ProductDomain('Drds', 'drds.aliyuncs.com'),
    new ProductDomain('M-kvstore', 'm-kvstore.aliyuncs.com'),
    new ProductDomain('Ram', 'ram.aliyuncs.com'),
    new ProductDomain('Cms', 'metrics.aliyuncs.com'),
    new ProductDomain('Crm', 'crm-cn-hangzhou.aliyuncs.com'),
    new ProductDomain('Ocs', 'pop-ocs.aliyuncs.com'),
    new ProductDomain('Ots', 'ots-pop.aliyuncs.com'),
    new ProductDomain('Dqs', 'dqs.aliyuncs.com'),
    new ProductDomain('Location', 'location.aliyuncs.com'),
    new ProductDomain('Ubsms', 'ubsms.aliyuncs.com'),
    new ProductDomain('Ubsms-inner', 'ubsms-inner.aliyuncs.com'),
];

$endpoint = new Endpoint('cn-hangzhou', $regionIds, $productDomains);
$endpoints = [$endpoint];
EndpointProvider::setEndpoints($endpoints);

define('ENABLE_HTTP_PROXY', false);
define('HTTP_PROXY_IP', '127.0.0.1');
define('HTTP_PROXY_PORT', '8888');


class StsToken
{

    public function getStsToken($region, $appId, $appKey, $policy, $roleArn, $clientId, $duration = 3600) {

        // 你需要操作的资源所在的region，STS服务目前只有杭州节点可以签发Token，签发出的Token在所有Region都可用
        // 只允许子用户使用角色
        $iClientProfile = DefaultProfile::getProfile($region, $appId, $appKey);
        $client = new DefaultAcsClient($iClientProfile);

        $request = new AssumeRoleRequest();
        // RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
        // 您可以使用您的客户的ID作为会话名称
        $request->setRoleSessionName($clientId);
        $request->setRoleArn($roleArn);
        $request->setPolicy($policy);
        $request->setDurationSeconds(3600);
        $response = $client->getAcsResponse($request);

        if (false === $response->isSuccess()) {
            return false;
        } else {
            $body = $response->getBody();
            if (empty($body)) {
                return false;
            }

            $content = json_decode($body, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                return false;
            }

            return $content;
        }
    }

}
