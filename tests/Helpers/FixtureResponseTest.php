<?php

it('can replace a path in a json response', function () {
    $response = mock_fixture('json')
        ->replacePath('data.0.id', 5)
        ->replacePath('data.0.name', 'Mary Jane')
        ->replacePath('data.1.id', 6)
        ->getMockResponse();

    $data = $response->body()->all();
    expect($data)->toBeArray()
        ->and($data['data'][0]['id'])->toBe(5)
        ->and($data['data'][0]['name'])->toBe('Mary Jane')
        ->and($data['data'][1]['id'])->toBe(6);
});

it('can replace a path and the text in a json response', function () {
    $response = mock_fixture('json')
        ->replacePath('has_more', true)
        ->replacePath('data.0.id', 5)
        ->replacePath('data.1.name', null)
        ->replaceString('{{TEST_NAME}}', 'Mary Jane')
        ->getMockResponse();

    $data = $response->body()->all();
    expect($data)->toBeArray()
        ->and($data['has_more'])->toBeTrue()
        ->and($data['data'][0]['id'])->toBe(5)
        ->and($data['data'][0]['name'])->toBe('Mary Jane')
        ->and($data['data'][1]['name'])->toBeNull();
});

it('can replace the text in a string response', function () {
    $response = mock_fixture('html')
        ->replaceString('TEST_HEAD', 'HELLO')
        ->replaceString('{{TEST_BODY}}', 'WORLD')
        ->getMockResponse();

    $data = $response->body()->all();
    expect($data)->toBeString()
        ->and($data)->toContain('HELLO')
        ->and($data)->toContain('WORLD');
});

it('can only replace text in a string response', function () {
    $response = mock_fixture('html')
        ->replacePath('TEST_HEAD', 'IGNORED')
        ->replaceString('{{TEST_BODY}}', 'WORLD')
        ->getMockResponse();

    $data = $response->body()->all();
    expect($data)->toBeString()
        ->and($data)->not->toContain('IGNORED')
        ->and($data)->toContain('WORLD');
});
