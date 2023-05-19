<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2023 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace Espo\Tools\Email\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Tools\Email\InboxService;

/**
 * Moves emails to a folder.
 */
class PostFolder implements Action
{
    public function __construct(private InboxService $inboxService) {}

    public function process(Request $request): Response
    {
        $folderId = $request->getRouteParam('folderId');

        if (!$folderId) {
            throw new BadRequest();
        }

        $data = $request->getParsedBody();

        $ids = $data->ids ?? null;
        $id = $data->id ?? null;

        if ($ids === null && is_string($id)) {
            $ids = [$id];
        }

        if (!is_array($ids)) {
            throw new BadRequest("No `ids`.");
        }

        if (count($ids) === 1) {
            $this->inboxService->moveToFolder($ids[0], $folderId);
        }

        $this->inboxService->moveToFolderIdList($ids, $folderId);

        return ResponseComposer::json(true);
    }
}
