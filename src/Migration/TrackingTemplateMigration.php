<?php

declare(strict_types=1);

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2021
 * @package    MenAtWork\MatomoTrackingTagBundle
 * @license    GNU/LGPL
 * @filesource
 */

namespace MenAtWork\MatomoTrackingTagBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * Updates tl_layout.piwikTemplate from previous versions.
 */
class TrackingTemplateMigration extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->db->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_layout'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_layout');

        if (!isset($columns['piwiktemplate'])) {
            return false;
        }

        return (bool) $this->db->fetchOne("SELECT COUNT(*) FROM tl_layout WHERE piwikTemplate = 'mod_piwikTrackingTagSynchron' OR piwikTemplate = 'mod_piwikTrackingTagAsynchron'");
    }

    public function run(): MigrationResult
    {
        $this->db->update('tl_layout', ['piwikTemplate' => 'mod_matomo_TrackingTagSynchron'], ['piwikTemplate' => 'mod_piwikTrackingTagSynchron']);
        $this->db->update('tl_layout', ['piwikTemplate' => 'mod_matomo_TrackingTagAsynchron'], ['piwikTemplate' => 'mod_piwikTrackingTagAsynchron']);

        return $this->createResult(true);
    }
}
