<?php

namespace cCrud\Traits;

/**
 * Trait para rastreamento de eventos
 * 
 * Suporta Google Analytics, Facebook Pixel, e outros pixels de rastreamento
 * 
 * @package cCrud\Traits
 * @version 2.0.0
 */
trait EventTracking
{
    /**
     * Configurações de rastreamento
     * @var array
     */
    protected array $trackingConfig = [
        'enabled' => false,
        'google_analytics' => [
            'enabled' => false,
            'tracking_id' => '', // GA4: G-XXXXXXXXXX
        ],
        'facebook_pixel' => [
            'enabled' => false,
            'pixel_id' => '',
        ],
        'google_ads' => [
            'enabled' => false,
            'conversion_id' => '',
            'conversion_label' => '',
        ],
        'linkedin' => [
            'enabled' => false,
            'partner_id' => '',
        ],
        'twitter' => [
            'enabled' => false,
            'pixel_id' => '',
        ],
    ];

    /**
     * Eventos rastreados
     * @var array
     */
    protected array $trackedEvents = [];

    /**
     * Configura o rastreamento
     * 
     * @param array $config Configurações
     * @return self
     */
    public function configureTracking(array $config): self
    {
        $this->trackingConfig = array_merge($this->trackingConfig, $config);
        $this->trackingConfig['enabled'] = true;

        return $this;
    }

    /**
     * Habilita o Google Analytics
     * 
     * @param string $trackingId ID de rastreamento (G-XXXXXXXXXX)
     * @return self
     */
    public function enableGoogleAnalytics(string $trackingId): self
    {
        $this->trackingConfig['google_analytics']['enabled'] = true;
        $this->trackingConfig['google_analytics']['tracking_id'] = $trackingId;
        $this->trackingConfig['enabled'] = true;

        return $this;
    }

    /**
     * Habilita o Facebook Pixel
     * 
     * @param string $pixelId ID do pixel
     * @return self
     */
    public function enableFacebookPixel(string $pixelId): self
    {
        $this->trackingConfig['facebook_pixel']['enabled'] = true;
        $this->trackingConfig['facebook_pixel']['pixel_id'] = $pixelId;
        $this->trackingConfig['enabled'] = true;

        return $this;
    }

    /**
     * Habilita o Google Ads
     * 
     * @param string $conversionId ID de conversão
     * @param string $conversionLabel Label de conversão
     * @return self
     */
    public function enableGoogleAds(string $conversionId, string $conversionLabel = ''): self
    {
        $this->trackingConfig['google_ads']['enabled'] = true;
        $this->trackingConfig['google_ads']['conversion_id'] = $conversionId;
        $this->trackingConfig['google_ads']['conversion_label'] = $conversionLabel;
        $this->trackingConfig['enabled'] = true;

        return $this;
    }

    /**
     * Habilita o LinkedIn Insight Tag
     * 
     * @param string $partnerId Partner ID
     * @return self
     */
    public function enableLinkedIn(string $partnerId): self
    {
        $this->trackingConfig['linkedin']['enabled'] = true;
        $this->trackingConfig['linkedin']['partner_id'] = $partnerId;
        $this->trackingConfig['enabled'] = true;

        return $this;
    }

    /**
     * Habilita o Twitter Pixel
     * 
     * @param string $pixelId ID do pixel
     * @return self
     */
    public function enableTwitterPixel(string $pixelId): self
    {
        $this->trackingConfig['twitter']['enabled'] = true;
        $this->trackingConfig['twitter']['pixel_id'] = $pixelId;
        $this->trackingConfig['enabled'] = true;

        return $this;
    }

    /**
     * Rastreia um evento
     * 
     * @param string $action Ação (create, edit, delete, etc.)
     * @param array $data Dados do evento
     * @return self
     */
    public function trackEvent(string $action, array $data = []): self
    {
        if (!$this->trackingConfig['enabled']) {
            return $this;
        }

        $event = [
            'action' => $action,
            'data' => $data,
            'timestamp' => time(),
        ];

        $this->trackedEvents[] = $event;

        return $this;
    }

    /**
     * Renderiza os scripts de rastreamento
     * 
     * @return string HTML dos scripts
     */
    public function renderTrackingScripts(): string
    {
        if (!$this->trackingConfig['enabled']) {
            return '';
        }

        $scripts = [];

        // Google Analytics
        if ($this->trackingConfig['google_analytics']['enabled']) {
            $scripts[] = $this->renderGoogleAnalyticsScript();
        }

        // Facebook Pixel
        if ($this->trackingConfig['facebook_pixel']['enabled']) {
            $scripts[] = $this->renderFacebookPixelScript();
        }

        // Google Ads
        if ($this->trackingConfig['google_ads']['enabled']) {
            $scripts[] = $this->renderGoogleAdsScript();
        }

        // LinkedIn
        if ($this->trackingConfig['linkedin']['enabled']) {
            $scripts[] = $this->renderLinkedInScript();
        }

        // Twitter
        if ($this->trackingConfig['twitter']['enabled']) {
            $scripts[] = $this->renderTwitterPixelScript();
        }

        return implode("\n", $scripts);
    }

    /**
     * Renderiza o script do Google Analytics
     * 
     * @return string
     */
    protected function renderGoogleAnalyticsScript(): string
    {
        $trackingId = $this->trackingConfig['google_analytics']['tracking_id'];

        return sprintf(
            "<!-- Google Analytics -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=%s\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '%s');
</script>",
            esc($trackingId),
            esc($trackingId)
        );
    }

    /**
     * Renderiza o script do Facebook Pixel
     * 
     * @return string
     */
    protected function renderFacebookPixelScript(): string
    {
        $pixelId = $this->trackingConfig['facebook_pixel']['pixel_id'];

        return sprintf(
            "<!-- Facebook Pixel -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '%s');
  fbq('track', 'PageView');
</script>
<noscript>
  <img height=\"1\" width=\"1\" style=\"display:none\"
       src=\"https://www.facebook.com/tr?id=%s&ev=PageView&noscript=1\"/>
</noscript>",
            esc($pixelId),
            esc($pixelId)
        );
    }

    /**
     * Renderiza o script do Google Ads
     * 
     * @return string
     */
    protected function renderGoogleAdsScript(): string
    {
        $conversionId = $this->trackingConfig['google_ads']['conversion_id'];

        return sprintf(
            "<!-- Google Ads -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=%s\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '%s');
</script>",
            esc($conversionId),
            esc($conversionId)
        );
    }

    /**
     * Renderiza o script do LinkedIn
     * 
     * @return string
     */
    protected function renderLinkedInScript(): string
    {
        $partnerId = $this->trackingConfig['linkedin']['partner_id'];

        return sprintf(
            "<!-- LinkedIn Insight Tag -->
<script type=\"text/javascript\">
_linkedin_partner_id = \"%s\";
window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
window._linkedin_data_partner_ids.push(_linkedin_partner_id);
</script><script type=\"text/javascript\">
(function(l) {
if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
window.lintrk.q=[]}
var s = document.getElementsByTagName(\"script\")[0];
var b = document.createElement(\"script\");
b.type = \"text/javascript\";b.async = true;
b.src = \"https://snap.licdn.com/li.lms-analytics/insight.min.js\";
s.parentNode.insertBefore(b, s);})(window.lintrk);
</script>
<noscript>
<img height=\"1\" width=\"1\" style=\"display:none;\" alt=\"\" src=\"https://px.ads.linkedin.com/collect/?pid=%s&fmt=gif\" />
</noscript>",
            esc($partnerId),
            esc($partnerId)
        );
    }

    /**
     * Renderiza o script do Twitter Pixel
     * 
     * @return string
     */
    protected function renderTwitterPixelScript(): string
    {
        $pixelId = $this->trackingConfig['twitter']['pixel_id'];

        return sprintf(
            "<!-- Twitter Pixel -->
<script>
!function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);
},s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='//static.ads-twitter.com/uwt.js',
a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');
twq('init','%s');
twq('track','PageView');
</script>",
            esc($pixelId)
        );
    }

    /**
     * Renderiza JavaScript para rastrear evento
     * 
     * @param string $action Ação
     * @param array $data Dados do evento
     * @return string
     */
    public function renderEventTracking(string $action, array $data = []): string
    {
        if (!$this->trackingConfig['enabled']) {
            return '';
        }

        $scripts = [];

        // Google Analytics
        if ($this->trackingConfig['google_analytics']['enabled']) {
            $scripts[] = sprintf(
                "gtag('event', '%s', %s);",
                esc($action),
                json_encode($data)
            );
        }

        // Facebook Pixel
        if ($this->trackingConfig['facebook_pixel']['enabled']) {
            $eventName = $this->mapActionToFacebookEvent($action);
            $scripts[] = sprintf(
                "fbq('track', '%s', %s);",
                $eventName,
                json_encode($data)
            );
        }

        // Google Ads Conversion
        if ($this->trackingConfig['google_ads']['enabled'] && $action === 'create') {
            $conversionLabel = $this->trackingConfig['google_ads']['conversion_label'];
            if ($conversionLabel) {
                $scripts[] = sprintf(
                    "gtag('event', 'conversion', {'send_to': '%s/%s'});",
                    esc($this->trackingConfig['google_ads']['conversion_id']),
                    esc($conversionLabel)
                );
            }
        }

        // LinkedIn
        if ($this->trackingConfig['linkedin']['enabled']) {
            $scripts[] = sprintf(
                "lintrk('track', { conversion_id: %d });",
                (int) $this->trackingConfig['linkedin']['partner_id']
            );
        }

        // Twitter
        if ($this->trackingConfig['twitter']['enabled']) {
            $twitterEvent = $this->mapActionToTwitterEvent($action);
            $scripts[] = sprintf(
                "twq('track', '%s');",
                $twitterEvent
            );
        }

        if (empty($scripts)) {
            return '';
        }

        return sprintf(
            "<script>\n%s\n</script>",
            implode("\n", $scripts)
        );
    }

    /**
     * Mapeia ação para evento do Facebook
     * 
     * @param string $action Ação
     * @return string Evento do Facebook
     */
    protected function mapActionToFacebookEvent(string $action): string
    {
        $map = [
            'create' => 'Lead',
            'edit' => 'UpdateInfo',
            'delete' => 'DeleteInfo',
            'view' => 'ViewContent',
            'search' => 'Search',
        ];

        return $map[$action] ?? 'CustomEvent';
    }

    /**
     * Mapeia ação para evento do Twitter
     * 
     * @param string $action Ação
     * @return string Evento do Twitter
     */
    protected function mapActionToTwitterEvent(string $action): string
    {
        $map = [
            'create' => 'tw-o9xzn-o9y0k',
            'edit' => 'tw-o9xzn-o9y0l',
            'delete' => 'tw-o9xzn-o9y0m',
        ];

        return $map[$action] ?? 'PageView';
    }

    /**
     * Obtém a configuração de rastreamento
     * 
     * @return array
     */
    public function getTrackingConfig(): array
    {
        return $this->trackingConfig;
    }

    /**
     * Obtém os eventos rastreados
     * 
     * @return array
     */
    public function getTrackedEvents(): array
    {
        return $this->trackedEvents;
    }
}
