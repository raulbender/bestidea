<?php

declare(strict_types=1);

namespace Framework\Tests\Unit;

use Framework\Http\Request;
use PHPUnit\Framework\TestCase;
use Nyholm\Psr7\ServerRequest;

class RequestTest extends TestCase {

    /**
     * Auxiliar para criar a Request nos testes sem repetir a lógica de Mock.
     */
    private function createRequest(
        array $query = [],
        array $post = [],
        array $server = [],
        ?string $content = null,
        array $headers = []
    ): Request {
        // Como o construtor agora é privado, precisamos de um "buraco na fechadura" 
        // ou usar o método estático oficial. 
        // Se você quer testar a Request isoladamente, pode adicionar um método 
        // estático 'createForTest' na classe Request apenas para isso.

        // Por enquanto, vamos simular via PSR-7 para garantir que os testes sejam reais:
        $psrRequest = new ServerRequest(
            $server['REQUEST_METHOD'] ?? 'GET',
            $server['REQUEST_URI'] ?? '/',
            $headers,
            $content,
            '1.1',
            $server
        );

        return Request::createFromPsr7($psrRequest->withQueryParams($query)->withParsedBody($post));
    }


    public function test_it_can_be_instantiated_with_custom_data(): void {
        $request = $this->createRequest(
            query: ['id' => '123'],
            post: ['name' => 'Volt Framework']
        );

        $this->assertEquals('123', $request->pull('id'));
        $this->assertEquals('Volt Framework', $request->pull('name'));
    }

    public function test_all_merges_query_and_post_data(): void {
        $request = $this->createRequest(
            query: ['source' => 'web'],
            post: ['action' => 'save']
        );

        $all = $request->all();

        $this->assertCount(2, $all);
        $this->assertArrayHasKey('source', $all);
        $this->assertArrayHasKey('action', $all);
    }

    public function test_string_method_returns_casted_value_or_default(): void {
        $request = $this->createRequest(query: [
            'username' => 'bill_dev',
            'tags' => ['php', 'r2'], // This is an array
        ]);

        $this->assertEquals('bill_dev', $request->pullString('username'));
        $this->assertEquals('guest', $request->pullString('not_found', 'guest'));

        // Should return default because 'tags' is an array, not a scalar
        $this->assertEquals('default_tags', $request->pullString('tags', 'default_tags'));
    }

    public function test_int_method_returns_integer_casted_value(): void {
        $request = $this->createRequest(query: ['age' => '25']);

        $this->assertSame(25, $request->pullInt('age'));
        $this->assertSame(0, $request->pullInt('non_existent'));
    }

    public function test_it_identifies_http_method_correctly(): void {
        $request = $this->createRequest(server: ['REQUEST_METHOD' => 'POST']);

        $this->assertEquals('POST', $request->method());
        $this->assertTrue($request->isPost());
    }



    public function test_it_retrieves_headers_properly(): void {
        $psrRequest = new \Nyholm\Psr7\ServerRequest(
            'GET',
            '/any',
            ['Authorization' => 'Bearer some-token', 'Content-Type' => 'application/json']
        );

        $request = Request::createFromPsr7($psrRequest);

        $this->assertEquals('Bearer some-token', $request->getHeader('authorization'));
        $this->assertEquals('application/json', $request->getHeader('Content-Type'));
    }


    public function test_get_path_removes_query_string_from_uri(): void {
        $request = $this->createRequest(server: [
            'REQUEST_URI' => '/users/profile?id=123&theme=dark',
        ]);

        $this->assertEquals('/users/profile', $request->getPath());
    }

    public function test_get_json_decodes_injected_content(): void {
        $jsonPayload = json_encode(['email' => 'test@volt.com', 'role' => 'admin']);
        $request = $this->createRequest(content: $jsonPayload);

        $data = $request->getJson();

        $this->assertIsArray($data);
        $this->assertEquals('test@volt.com', $data['email']);
        $this->assertEquals('admin', $data['role']);
    }

    public function test_base_url_detection(): void {
        $request = $this->createRequest(server: [
            'HTTPS' => 'on',
            'HTTP_HOST' => 'volt.local',
        ]);

        $this->assertEquals('https://volt.local', $request->getBaseUrl());
    }
}
