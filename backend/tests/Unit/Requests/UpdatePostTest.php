<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\User\UploadPost;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdatePostTest extends TestCase
{
    /**
     * @param array
     * @param array
     * @param boolean
     * @dataProvider dataProvider
     */
    public function test_validation(array $keys, array $values, bool $expect)
    {
        $dataList = array_combine($keys, $values);

        $rules = (new UploadPost())->rules();

        $validator = Validator::make($dataList, $rules);

        $this->assertEquals($expect, $validator->passes());
    }

    public function dataProvider(): array
    {
        return [
            'fail no users' => [
                [
                    'dummy key'
                ],
                [
                    'dummy val'
                ],
                false
            ]
        ];
    }
}
