<?php

namespace Rorikurn\Activator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

class ResendActivationController
{
    /**
     * The validation factory implementation.
     *
     * @var ValidationFactory
     */
    protected $validation;

    /**
     * Create a resend activation controller instance.
     *
     * @param  ValidationFactory  $validation
     * @return void
     */
    public function __construct(ValidationFactory $validation)
    {
        $this->validation = $validation;
    }

    /**
     * Store a new client.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validation->make($request->all(), [
            'token' => 'required|max:255',
            'email' => 'required|url',
        ])->validate();
        
        return $this->clients->create(
            $request->user()->getKey(), $request->name, $request->redirect
        )->makeVisible('secret');
    }
    /**
     * Update the given client.
     *
     * @param  Request  $request
     * @param  string  $clientId
     * @return Response
     */
    public function update(Request $request, $clientId)
    {
        if (! $request->user()->clients->find($clientId)) {
            return new Response('', 404);
        }
        $this->validation->make($request->all(), [
            'name' => 'required|max:255',
            'redirect' => 'required|url',
        ])->validate();
        return $this->clients->update(
            $request->user()->clients->find($clientId),
            $request->name, $request->redirect
        );
    }
    /**
     * Delete the given client.
     *
     * @param  Request  $request
     * @param  string  $clientId
     * @return Response
     */
    public function destroy(Request $request, $clientId)
    {
        if (! $request->user()->clients->find($clientId)) {
            return new Response('', 404);
        }
        $this->clients->delete(
            $request->user()->clients->find($clientId)
        );
    }
}