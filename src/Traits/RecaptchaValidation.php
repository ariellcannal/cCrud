<?php

namespace cCrud\Traits;

use Config\Services;

/**
 * Trait para validação reCAPTCHA
 * 
 * Suporta reCAPTCHA v2 e v3 do Google
 * 
 * @package cCrud\Traits
 * @version 2.0.0
 */
trait RecaptchaValidation
{
    /**
     * Configurações do reCAPTCHA
     * @var array
     */
    protected array $recaptchaConfig = [
        'enabled' => false,
        'site_key' => '',
        'secret_key' => '',
        'version' => 'v3', // v2 ou v3
        'min_score' => 0.5, // Para v3
        'actions' => ['create', 'edit', 'delete'], // Ações que requerem validação
    ];

    /**
     * Configura o reCAPTCHA
     * 
     * @param array $config Configurações
     * @return self
     */
    public function configureRecaptcha(array $config): self
    {
        $this->recaptchaConfig = array_merge($this->recaptchaConfig, $config);
        return $this;
    }

    /**
     * Habilita o reCAPTCHA
     * 
     * @param string $siteKey Site key do Google
     * @param string $secretKey Secret key do Google
     * @param string $version Versão (v2 ou v3)
     * @return self
     */
    public function enableRecaptcha(string $siteKey, string $secretKey, string $version = 'v3'): self
    {
        $this->recaptchaConfig['enabled'] = true;
        $this->recaptchaConfig['site_key'] = $siteKey;
        $this->recaptchaConfig['secret_key'] = $secretKey;
        $this->recaptchaConfig['version'] = $version;

        return $this;
    }

    /**
     * Desabilita o reCAPTCHA
     * 
     * @return self
     */
    public function disableRecaptcha(): self
    {
        $this->recaptchaConfig['enabled'] = false;
        return $this;
    }

    /**
     * Define as ações que requerem reCAPTCHA
     * 
     * @param array $actions Array de ações (create, edit, delete, etc.)
     * @return self
     */
    public function setRecaptchaActions(array $actions): self
    {
        $this->recaptchaConfig['actions'] = $actions;
        return $this;
    }

    /**
     * Define o score mínimo para reCAPTCHA v3
     * 
     * @param float $score Score entre 0.0 e 1.0
     * @return self
     */
    public function setRecaptchaMinScore(float $score): self
    {
        $this->recaptchaConfig['min_score'] = max(0.0, min(1.0, $score));
        return $this;
    }

    /**
     * Verifica se o reCAPTCHA está habilitado para uma ação
     * 
     * @param string $action Ação (create, edit, delete, etc.)
     * @return bool
     */
    public function isRecaptchaEnabledForAction(string $action): bool
    {
        return $this->recaptchaConfig['enabled'] 
            && in_array($action, $this->recaptchaConfig['actions']);
    }

    /**
     * Valida o token reCAPTCHA
     * 
     * @param string|null $token Token do reCAPTCHA
     * @param string $action Ação sendo executada
     * @return array Resultado da validação ['success' => bool, 'score' => float|null, 'error' => string|null]
     */
    public function validateRecaptcha(?string $token, string $action = 'submit'): array
    {
        // Se não está habilitado, retorna sucesso
        if (!$this->recaptchaConfig['enabled']) {
            return ['success' => true, 'score' => null, 'error' => null];
        }

        // Se não está habilitado para esta ação, retorna sucesso
        if (!$this->isRecaptchaEnabledForAction($action)) {
            return ['success' => true, 'score' => null, 'error' => null];
        }

        // Verificar se o token foi fornecido
        if (empty($token)) {
            return [
                'success' => false,
                'score' => null,
                'error' => 'Token reCAPTCHA não fornecido'
            ];
        }

        // Fazer requisição para a API do Google
        $response = $this->verifyRecaptchaToken($token);

        if (!$response['success']) {
            return [
                'success' => false,
                'score' => null,
                'error' => $response['error'] ?? 'Falha na verificação reCAPTCHA'
            ];
        }

        // Para v3, verificar o score
        if ($this->recaptchaConfig['version'] === 'v3') {
            $score = $response['score'] ?? 0.0;

            if ($score < $this->recaptchaConfig['min_score']) {
                return [
                    'success' => false,
                    'score' => $score,
                    'error' => sprintf(
                        'Score reCAPTCHA muito baixo (%.2f < %.2f)',
                        $score,
                        $this->recaptchaConfig['min_score']
                    )
                ];
            }

            return [
                'success' => true,
                'score' => $score,
                'error' => null
            ];
        }

        // Para v2, apenas verificar sucesso
        return [
            'success' => true,
            'score' => null,
            'error' => null
        ];
    }

    /**
     * Verifica o token na API do Google
     * 
     * @param string $token Token do reCAPTCHA
     * @return array Resposta da API
     */
    protected function verifyRecaptchaToken(string $token): array
    {
        $secretKey = $this->recaptchaConfig['secret_key'];

        if (empty($secretKey)) {
            return [
                'success' => false,
                'error' => 'Secret key do reCAPTCHA não configurada'
            ];
        }

        try {
            $client = Services::curlrequest();

            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ]
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            if (!$data) {
                return [
                    'success' => false,
                    'error' => 'Resposta inválida da API reCAPTCHA'
                ];
            }

            return $data;

        } catch (\Exception $e) {
            log_message('error', 'Erro ao verificar reCAPTCHA: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Erro ao comunicar com a API reCAPTCHA'
            ];
        }
    }

    /**
     * Renderiza o script do reCAPTCHA
     * 
     * @return string HTML do script
     */
    public function renderRecaptchaScript(): string
    {
        if (!$this->recaptchaConfig['enabled']) {
            return '';
        }

        $siteKey = $this->recaptchaConfig['site_key'];
        $version = $this->recaptchaConfig['version'];

        if ($version === 'v3') {
            return sprintf(
                '<script src="https://www.google.com/recaptcha/api.js?render=%s"></script>',
                esc($siteKey)
            );
        } else {
            return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        }
    }

    /**
     * Renderiza o widget do reCAPTCHA v2
     * 
     * @return string HTML do widget
     */
    public function renderRecaptchaWidget(): string
    {
        if (!$this->recaptchaConfig['enabled'] || $this->recaptchaConfig['version'] !== 'v2') {
            return '';
        }

        $siteKey = $this->recaptchaConfig['site_key'];

        return sprintf(
            '<div class="g-recaptcha" data-sitekey="%s"></div>',
            esc($siteKey)
        );
    }

    /**
     * Renderiza o JavaScript para reCAPTCHA v3
     * 
     * @param string $action Ação sendo executada
     * @param string $callback Função de callback JavaScript
     * @return string JavaScript
     */
    public function renderRecaptchaV3Script(string $action = 'submit', string $callback = 'onRecaptchaSuccess'): string
    {
        if (!$this->recaptchaConfig['enabled'] || $this->recaptchaConfig['version'] !== 'v3') {
            return '';
        }

        $siteKey = $this->recaptchaConfig['site_key'];

        return sprintf(
            "<script>
            function executeRecaptcha() {
                grecaptcha.ready(function() {
                    grecaptcha.execute('%s', {action: '%s'}).then(function(token) {
                        document.getElementById('recaptcha-token').value = token;
                        if (typeof %s === 'function') {
                            %s(token);
                        }
                    });
                });
            }
            </script>",
            esc($siteKey),
            esc($action),
            $callback,
            $callback
        );
    }

    /**
     * Renderiza o campo hidden para o token do reCAPTCHA
     * 
     * @return string HTML do campo hidden
     */
    public function renderRecaptchaTokenField(): string
    {
        if (!$this->recaptchaConfig['enabled']) {
            return '';
        }

        return '<input type="hidden" id="recaptcha-token" name="recaptcha_token" value="">';
    }

    /**
     * Obtém a configuração do reCAPTCHA
     * 
     * @return array
     */
    public function getRecaptchaConfig(): array
    {
        return $this->recaptchaConfig;
    }
}
