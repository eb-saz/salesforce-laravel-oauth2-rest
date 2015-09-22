<?php

namespace Frankkessler\Salesforce\Repositories\Eloquent;

use Frankkessler\Salesforce\Repositories\TokenRepositoryInterface;
use Frankkessler\Salesforce\Models\SalesforceToken;
use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Auth;
use Config;

class TokenEloquentRepository implements TokenRepositoryInterface{

    public function getAccessToken($user_id=null){
        $record = $this->getTokenRecord($user_id);
        return SalesforceToken::findByUserId($user_id)->first();
    }
    public function getRefreshToken($user_id=null){
        $record = $this->getTokenRecord($user_id);
        return SalesforceToken::findByUserId($user_id)->first();
    }

    public function setAccessToken($access_token, $user_id=null){
        $record = $this->getTokenRecord($user_id);

        $record->access_token = $access_token;
        $record->save();
    }

    public function setRefreshToken($refresh_token, $user_id=null){
        $record = $this->getTokenRecord($user_id);

        $record->refresh_token = $refresh_token;
        $record->save();
    }

    public function getTokenRecord($user_id=null){
        if(is_null($user_id)){
            if(!$user_id = Config::get('salesforce.storage_global_user_id')){
                $user = Auth::user();
                if($user){
                    $user_id = $user->id;
                }else{
                    $user_id = 0;
                }
            }
        }

        $record = SalesforceToken::findByUserId($user_id)->first();

        if(!$record) {
            $record = new SalesforceToken;
            $record->user_id = $user_id;
        }
        return $record;
    }

    public function setTokenRecord(AccessToken $token, $user_id=null){
        if(is_null($user_id)){
            if(!$user_id = Config::get('salesforce.storage_global_user_id')){
                $user = Auth::user();
                if($user){
                    $user_id = $user->id;
                }else{
                    $user_id = 0;
                }
            }
        }

        $record = SalesforceToken::findByUserId($user_id)->first();

        if(!$record) {
            $record = new SalesforceToken;
            $record->user_id = $user_id;
        }

        $token_data = $token->getData();

        $record->access_token = $token->getToken();
        $record->refresh_token = $token->getRefreshToken()->getToken();
        $record->instance_base_url = $token_data['instance_url'];

        $record->save();

        return $record;
    }
}