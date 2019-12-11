<?php
namespace App\Application\Actions\Token;

class Token
{
	public $decoded;
	public function hydrate($decoded)
	{
		$this->decoded = $decoded;
	}
	public function hasScope(array $scope)
	{
		if (empty($this->decoded)) {
			return false;
		}
		return !!count(array_intersect($scope, $this->decoded->scope));
	}
	
	public function getScopes() {
		return $this->decoded->scope;
	}
	public function getSub() {
		return $this->decoded->sub;
	}

	static public function validScopes () {
		$valid_scopes = [
            "items.create",
            "items.read",
            "items.update",
            "items.delete",
            "items.list",
            "items.all",
            "reservations.create",
            "reservations.create.owner",
            "reservations.create.owner.donation_only",
            "reservations.read",
            "reservations.update",
            "reservations.update.owner",
            "reservations.delete",
            "reservations.delete.owner",
            "reservations.list",
            "reservations.all",
            "consumers.create",
            "consumers.read",
            "consumers.update",
            "consumers.delete",
            "consumers.list",
            "consumers.all",
            "events.create",
            "events.read",
            "events.update",
            "events.delete",
            "events.list",
            "events.all",
            "users.create",
            "users.read",
            "users.read.owner",
            "users.read.state",
            "users.update",
            "users.update.owner",
            "users.update.password",
            "users.delete",
            "users.list",
            "users.all",
            "payments.all",
            "payments.list",
            "lendings.all",
            "lendings.list",
            "auth.confirm",
            "enrolment.confirm"
		];
		return $valid_scopes;
	}
	static public function resetPwdScopes () {
		$reset_pwd_scopes = [
				"users.update.password"
		];
		return $reset_pwd_scopes;
	}
	static public function emailLinkScopes () {
		$reset_pwd_scopes = [
            "users.read.owner", // not allowed to consult other users info
            "users.update.password",
            "users.update.owner" // not allowed to update other users info
		];
		return $reset_pwd_scopes;
	}

	static public function allowedScopes($role) {
		if ($role == 'admin') {
			return [
				"items.all",
				"reservations.all",
				"consumers.all",
				"users.all",
                "events.all",
                "payments.all",
                "lendings.all",
                "users.read.owner", // need to be added for check against emailLinkScopes
                "users.update.password", // need to be added for check against resetPwdScopes, emailLinkScopes
                "users.update.owner", // need to be added for check against emailLinkScopes
                "enrolment.confirm"
			];
		}
		if ($role == 'member') {
			return [
					"items.read",
					"items.list",
					"reservations.create.owner",
					"reservations.read",
					"reservations.update.owner",
					"reservations.delete.owner",
					"reservations.list",
					"consumers.read",
					"consumers.list",
					"users.read.owner", // not allowed to consult other users info
					"users.update.password",
					"users.update.owner", // not allowed to update other users info
			];
		}
		if ($role == 'supporter') {
			return [
					"items.read",
					"items.list",
					"reservations.create.owner.donation_only", // allow reservations on donated tools only
					"reservations.read",
					"reservations.update.owner",
					"reservations.delete.owner",
					"reservations.list",
					"consumers.read",
					"consumers.list",
					"users.read.owner", // not allowed to consult other users info
					"users.update.password",
					"users.update.owner", // not allowed to update other users info
			];
		}
		// unknown role / guest
		return [
				"items.read",
				"items.list",
				"reservations.read",
				"reservations.list",
				"consumers.read",
				"consumers.list",
				"auth.confirm",
                "users.read.state"
		];
	}
}