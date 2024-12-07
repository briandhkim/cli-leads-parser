<?php 

namespace App;

use App\Tools\File;

class LeadsParser
{
    private $leads;
    private $updatedLeads;
    private $duplicateLeads;
    private $updatedFilePath;
    private $logPath;

    /**
     * Rule #2: Duplicate IDs count as dups. Duplicate emails count as dups.
     * Both must be unique in our dataset. Duplicate values elsewhere do not count as dups.
     */
    private $uniqueFields = [
        '_id',
        'email'
    ];

    public function __construct(array $leads = [])
    {
        $this->leads = $leads;
    }

    public function deduplicate(): array
    {
        $this->filterData();

        $this->logChanges();

        $this->saveUpdatedRecords();

        $output = $this->getResultOutput();
        $output['updatedFilePath'] = $this->updatedFilePath;
        $output['logPath'] = $this->logPath;

        return $output;
    }

    private function filterData()
    {
        $filteredLeads = $this->leads['leads'];

        foreach($this->uniqueFields as $field) {
            $groupedLeads = $this->groupByField($filteredLeads, $field);

            $leadsToKeep = $this->determineLeadsToPreserve($groupedLeads);

            $filteredLeads = $this->filterDuplicates($filteredLeads, $leadsToKeep, $field);
        }

        $this->updatedLeads['leads'] = array_values($filteredLeads);
    }

    private function determineLeadsToPreserve(array $leadsByField): array
    {
        $leadsToKeep = [];

        foreach($leadsByField as $field => $leads) {
            $maxTimestamp = 0;
            $leadToKeep = null;
            foreach ($leads as $lead) {
                $date = $lead['entryDate'];
                $timestamp = strtotime($date);
                /**
                 * greater-than-or-equal comparison:
                 * rule #1 - The data from the newest date should be preferred
                 * rule #3 - If the dates are identical, the data from the 
                 * record provided last in the list should be preferred
                 */
                if ($timestamp >= $maxTimestamp) {
                    $maxTimestamp = $timestamp;
                    $leadToKeep = $lead;
                }
            }
            
            $leadsToKeep[$this->getLeadKey($leadToKeep)] = $leadToKeep;
        }

        return $leadsToKeep;
    }

    private function groupByField(array $leads, string $field): array 
    {
        $groupedLeads = [];

        foreach($leads as $lead) {
            $groupedLeads[$lead[$field]][] = $lead;
        }

        return  $groupedLeads;
    }

    private function filterDuplicates(array $leads, array $leadsToKeep, string $field): array
    {
        foreach($leads as $idx => $lead) {
            $leadKey = $this->getLeadKey($lead);

            if (empty($leadsToKeep[$leadKey])) {
                $this->duplicateLeads[$field][] = $lead;
                unset($leads[$idx]);
            }
        }

        return $leads;
    }

    private function logChanges()
    {
        $logName = time() . "_duplicate_leads_parser_results.log";
        $this->logPath = 'storage/logs/leads/' . $logName;

        File::write('storage/logs/leads/', $logName, $this->getResultOutput());
    }

    private function saveUpdatedRecords()
    {
        $jsonFileName = time() . "_updated_leads.json";
        $this->updatedFilePath = 'storage/leads/' . $jsonFileName;

        File::write('storage/leads/', $jsonFileName, $this->updatedLeads);
    }

    private function getLeadKey(array $lead): string
    {
        return $lead['_id'] . '-' . $lead['email'];
    }

    private function getDuplicatedLeadsCount(): int
    {
        if (empty($this->duplicateLeads)) return 0;

        $count = 0;

        foreach($this->duplicateLeads as $field => $leads) {
            $count += count($leads);
        }

        return $count;
    }

    private function getResultOutput()
    {
        return [
            'originalLeads' => $this->leads,
            'originalLeadsCount' => count($this->leads['leads']),
            'leadsPreserved' => $this->updatedLeads,
            'leadsPreservedCount' => count($this->updatedLeads['leads']),
            'duplicatesRemoved' => $this->duplicateLeads,
            'duplicatesRemovedCount' => $this->getDuplicatedLeadsCount()
        ];
    }
}