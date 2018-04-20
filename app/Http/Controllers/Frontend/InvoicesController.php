<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

class InvoicesController extends CommonController
{
    public function create(Request $request)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {
                $data = array(
                    'userid' => $currentUser['id'],
                    'name' => $request->input('name')
                );
                $this->InvoiceValidator->with($data)->passesOrFail(ValidatorInterface::RULE_CREATE);
                $invoice = $this->InvoiceRepository->create($data);
                $response = [
                    'message' => 'Invoice created.',
                    'retcode'  => 0
                ];

            } catch (ValidatorException $e) {
                $response = [
                    'message' => $e->getMessageBag(),
                    'retcode'  => 1
                ];
            }
            return response()->json($response);
        }
        else
        {
            return $this->index();
        }

    }

    public function update(Request $request)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {
                $id = $request->input('id');
                $data = array(
                    'userid' => $currentUser['id'],
                    'name' => $request->input('name')
                );
                $this->InvoiceValidator->with($data)->passesOrFail(ValidatorInterface::RULE_UPDATE);
                $invoice = $this->InvoiceRepository->update($data,$id);
                $response = [
                    'message' => 'Invoice updated.',
                    'retcode'  => 0
                ];

            } catch (ValidatorException $e) {
                $response = [
                    'message' => $e->getMessageBag(),
                    'retcode'  => 1
                ];
            }
            return response()->json($response);
        }
        else
        {
            return $this->index();
        }

    }

    public function delete(Request $request)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {
                $id = $request->input('id');
                $invoice = $this->InvoiceRepository->delete($id);
                $response = [
                    'message' => 'Invoice deleted.',
                    'retcode'  => 0
                ];

            } catch (ValidatorException $e) {
                $response = [
                    'message' => $e->getMessageBag(),
                    'retcode'  => 1
                ];
            }
            return response()->json($response);
        }
        else
        {
            return $this->index();
        }

    }

}
