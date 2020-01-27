<?php

namespace Servidor\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Servidor\Http\Controllers\Controller;
use Servidor\System\Group as SystemGroup;

class GroupsController extends Controller
{
    public function index(): Response
    {
        return response(SystemGroup::list());
    }

    public function store(Request $request): Response
    {
        $data = $request->validate($this->validationRules());

        try {
            $group = SystemGroup::create($data['name'], $data['gid'] ?? null);
        } catch (GroupSaveException $e) {
            $data['error'] = $e->getMessage();

            return response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response($group, Response::HTTP_CREATED);
    }

    /**
     * Update the specified group on the system.
     *
     * @param int $gid
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $gid)
    {
        $data = $request->validate($this->validationRules());
        $data['gid'] = (int) ($data['gid'] ?? $gid);

        if (!$original = posix_getgrgid($gid)) {
            throw $this->failed('No group found matching the given criteria.');
        }
        $updated = $original;

        if ($data['name'] != $original['name']) {
            $options[] = '-n ' . $data['name'];
        }

        if ($data['gid'] != $gid && $data['gid'] > 0) {
            $options[] = '-g ' . $data['gid'];
        }

        if (($data['users'] ?? []) != $original['members']) {
            $members = implode(',', $data['users']);
        }

        if (empty($options ?? null) && !isset($members)) {
            throw $this->failed('Nothing to update!');
        }

        if (count($options ?? [])) {
            $options[] = $original['name'];

            exec('sudo groupmod ' . implode(' ', $options), $output, $retval);

            if (0 !== $retval) {
                throw $this->failed('Something went wrong updating the group. Exit code: ' . $retval);
            }

            $updated = posix_getgrgid($data['gid']);
        }

        if (isset($members)) {
            $group = $updated['name'];

            exec("sudo gpasswd -M '" . ($members ?? null) . "' {$group}", $output, $retval);

            if (0 !== $retval) {
                throw $this->failed('Something went wrong updating the group users. Exit code: ' . $retval);
            }

            $updated = posix_getgrgid($data['gid']);
        }

        return response([
            'gid' => $updated['gid'],
            'name' => $updated['name'],
            'users' => $updated['members'],
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified group from the system.
     *
     * @param int $gid
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($gid)
    {
        if ($group = posix_getgrgid($gid)) {
            exec('sudo groupdel ' . $group['name']);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get the validation rules for system groups.
     *
     * @return array
     */
    protected function validationRules()
    {
        return [
            'name' => [
                'required', 'max:32', 'bail',
                function ($attribute, $value, $fail): void {
                    if (Str::contains($value, ':')) {
                        $fail("The {$attribute} cannot contain a colon.");
                    }

                    if (Str::contains($value, ',')) {
                        $fail("The {$attribute} cannot contain a comma.");
                    }

                    if (Str::contains($value, ["\t", "\n", ' '])) {
                        $fail("The {$attribute} cannot contain whitespace or newlines.");
                    }
                },
                'regex:/^[a-z_][a-z0-9_-]*[\$]?$/',
            ],
            'gid' => 'integer|nullable',
            'groups' => 'array|nullable',
            'users' => 'array|nullable',
        ];
    }

    protected function failed($message, $key = 'gid')
    {
        return ValidationException::withMessages([
            $key => $message,
        ]);
    }
}
