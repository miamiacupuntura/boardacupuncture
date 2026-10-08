<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace aiprovider_openai;

use core\http_client;
use core_ai\ai_image;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class process image generation.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_generate_image extends abstract_processor {
    /** @var int The number of images to generate dall-e-3 only supports 1. */
    private int $numberimages = 1;

    /** @var string Response format: url or b64_json. */
    private string $responseformat = 'url';

    #[\Override]
    protected function get_endpoint(): UriInterface {
        return new Uri(get_config('aiprovider_openai', 'action_generate_image_endpoint'));
    }

    #[\Override]
    protected function get_model(): string {
        return get_config('aiprovider_openai', 'action_generate_image_model');
    }

    #[\Override]
    protected function query_ai_api(): array {
        $response = parent::query_ai_api();

        // Store GPT Image base64 responses or legacy image URLs.
        if ($response['success']) {
            if (!empty($response['imagebase64'])) {
                $fileobj = $this->base64_to_file(
                    $this->action->get_configuration('userid'),
                    $response['imagebase64']
                );
            } else {
                $fileobj = $this->url_to_file(
                    $this->action->get_configuration('userid'),
                    $response['sourceurl']
                );
            }
            // Add the file to the response, so the calling placement can do whatever they want with it.
            $response['draftfile'] = $fileobj;
        }

        return $response;
    }

    /**
     * Convert the given aspect ratio to an image size
     * that is compatible with the OpenAI API.
     *
     * @param string $ratio The aspect ratio of the image.
     * @return string The size of the image.
     */
    private function calculate_size(string $ratio): string {
        if ($ratio === 'square') {
            $size = '1024x1024';
        } else if ($ratio === 'landscape') {
            $size = '1792x1024';
        } else if ($ratio === 'portrait') {
            $size = '1024x1792';
        } else {
            throw new \coding_exception('Invalid aspect ratio: ' . $ratio);
        }
        return $size;
    }

    #[\Override]
    protected function create_request_object(string $userid): RequestInterface {
        $model = $this->get_model();

        if ($model === 'gpt-image-1-mini') {
            $body = [
                'model' => $model,
                'prompt' => $this->action->get_configuration('prompttext'),
                'n' => 1,
                'size' => '1024x1024',
                'quality' => 'low',
                'user' => $userid,
            ];
        } else {
            $body = [
                'prompt' => $this->action->get_configuration('prompttext'),
                'model' => $model,
                'n' => $this->numberimages,
                'quality' => $this->action->get_configuration('quality'),
                'size' => $this->calculate_size(
                    $this->action->get_configuration('aspectratio')
                ),
                'user' => $userid,
            ];
        }

        return new Request(
            method: 'POST',
            uri: '',
            body: json_encode((object) $body),
            headers: ['Content-Type' => 'application/json'],
        );
    }

    #[\Override]
    protected function handle_api_success(ResponseInterface $response): array {
        $responsebody = $response->getBody();
        $bodyobj = json_decode($responsebody->getContents());

        return [
            'success' => true,
            'sourceurl' => $bodyobj->data[0]->url ?? '',
            'imagebase64' => $bodyobj->data[0]->b64_json ?? '',
            'revisedprompt' => $bodyobj->data[0]->revised_prompt ?? '',
        ];
    }

    /**
     * Convert the url for the image to a file.
     *
     * Placements can't interact with the provider AI directly,
     * therefore we need to provide the image file in a format that can
     * be used by placements. So we use the file API.
     *
     * @param int $userid The user id.
     * @param string $url The URL to the image.
     * @return \stored_file The file object.
     */
    private function base64_to_file(int $userid, string $encoded): \stored_file {
        $binary = base64_decode($encoded, true);
        if ($binary === false) {
            throw new \moodle_exception('Invalid image data.');
        }

        $tempdst = make_request_directory() . '/ai_flashcard_' .
            bin2hex(random_bytes(8)) . '.png';

        file_put_contents($tempdst, $binary);

        $image = new ai_image($tempdst);
        $image->add_watermark()->save();

        $fileinfo = new \stdClass();
        $fileinfo->contextid = \context_user::instance($userid)->id;
        $fileinfo->filearea = 'draft';
        $fileinfo->component = 'user';
        require_once($GLOBALS['CFG']->libdir . '/filelib.php');
        $fileinfo->itemid = \file_get_unused_draft_itemid();
        $fileinfo->filepath = '/';
        $fileinfo->filename = basename($tempdst);

        return get_file_storage()->create_file_from_pathname(
            $fileinfo,
            $tempdst
        );
    }

    private function url_to_file(int $userid, string $url): \stored_file {
        global $CFG;

        require_once("{$CFG->libdir}/filelib.php");

        $parsedurl = parse_url($url, PHP_URL_PATH); // Parse the URL to get the path.
        $filename = basename($parsedurl); // Get the basename of the path.

        $client = \core\di::get(http_client::class);

        // Download the image and add the watermark.
        $tempdst = make_request_directory() . DIRECTORY_SEPARATOR . $filename;
        $client->get($url, [
            'sink' => $tempdst,
            'timeout' => $CFG->repositorygetfiletimeout,
        ]);

        $image = new ai_image($tempdst);
        $image->add_watermark()->save();

        // We put the file in the user draft area initially.
        // Placements (on behalf of the user) can then move it to the correct location.
        $fileinfo = new \stdClass();
        $fileinfo->contextid = \context_user::instance($userid)->id;
        $fileinfo->filearea = 'draft';
        $fileinfo->component = 'user';
        require_once($GLOBALS['CFG']->libdir . '/filelib.php');
        $fileinfo->itemid = \file_get_unused_draft_itemid();
        $fileinfo->filepath = '/';
        $fileinfo->filename = $filename;

        $fs = get_file_storage();
        return $fs->create_file_from_string($fileinfo, file_get_contents($tempdst));
    }
}
