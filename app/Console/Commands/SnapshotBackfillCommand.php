<?php
/**
 * 历史数据快照字段回填命令
 *
 * 用途：为迁移前已存在的历史记录，使用当前主数据名称填充空白快照字段。
 * 注意：此命令只修改快照字段为 NULL 或空字符串的记录，不会覆盖已有快照值。
 *
 * 运行方式：php artisan snapshot:backfill [--dry-run]
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SnapshotBackfillCommand extends Command
{
    protected $signature = 'snapshot:backfill
                            {--dry-run : 仅统计需要回填的数量，不实际写入}';

    protected $description = '回填历史记录中的快照字段（shipping_company_name、company_header_name、fee_type_name 等）';

    private bool $dryRun = false;
    private int $totalUpdated = 0;

    public function handle(): int
    {
        $this->dryRun = (bool)$this->option('dry-run');

        if ($this->dryRun) {
            $this->warn('=== DRY-RUN 模式：只统计，不写入 ===');
        } else {
            $this->info('=== 开始回填历史快照字段 ===');
        }

        $this->backfillOrdersShippingCompany();
        $this->backfillOrdersEnteredPortWharf();
        $this->backfillOrdersOriginHarbor();
        $this->backfillOrdersDestinationHarbor();
        $this->backfillOrderDelegationHeaders();
        $this->backfillContainerTypes();
        $this->backfillContainerFleets();
        $this->backfillContainerWharves();
        $this->backfillContainerLoadingAddresses();
        $this->backfillOrderPaymentsCompanyHeader();
        $this->backfillOrderReceiptsCompanyHeader();
        $this->backfillInvoicesInvoiceType();
        $this->backfillInvoiceItemsFeeType();
        $this->backfillOrderBillItemsFeeType();

        $this->newLine();
        $this->info("=== 回填完成，共更新 {$this->totalUpdated} 条记录 ===");

        return self::SUCCESS;
    }

    private function backfillOrdersShippingCompany(): void
    {
        $this->line('→ orders.shipping_company_name ...');
        $count = 0;

        DB::table('orders')
            ->whereNotNull('shipping_company_id')
            ->where(function ($q) {
                $q->whereNull('shipping_company_name')
                    ->orWhere('shipping_company_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('shipping_companies')->where('id', $row->shipping_company_id)->value('name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('orders')->where('id', $row->id)->update(['shipping_company_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('orders.shipping_company_name', $count);
    }

    private function backfillOrdersEnteredPortWharf(): void
    {
        $this->line('→ orders.entered_port_wharf_name ...');
        $count = 0;

        DB::table('orders')
            ->whereNotNull('entered_port_wharf_id')
            ->where(function ($q) {
                $q->whereNull('entered_port_wharf_name')
                    ->orWhere('entered_port_wharf_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('wharves')->where('id', $row->entered_port_wharf_id)->value('name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('orders')->where('id', $row->id)->update(['entered_port_wharf_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('orders.entered_port_wharf_name', $count);
    }

    private function backfillOrdersOriginHarbor(): void
    {
        $this->line('→ orders.origin_harbor ...');
        $count = 0;

        DB::table('orders')
            ->whereNotNull('origin_harbor_id')
            ->where(function ($q) {
                $q->whereNull('origin_harbor')
                    ->orWhere('origin_harbor', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('harbors')->where('id', $row->origin_harbor_id)->value('name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('orders')->where('id', $row->id)->update([
                                'origin_harbor' => json_encode($name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                            ]);
                        }
                    }
                }
            });

        $this->printResult('orders.origin_harbor', $count);
    }

    private function backfillOrdersDestinationHarbor(): void
    {
        $this->line('→ orders.destination_harbor ...');
        $count = 0;

        DB::table('orders')
            ->whereNotNull('destination_harbor_id')
            ->where(function ($q) {
                $q->whereNull('destination_harbor')
                    ->orWhere('destination_harbor', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('harbors')->where('id', $row->destination_harbor_id)->value('name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('orders')->where('id', $row->id)->update([
                                'destination_harbor' => json_encode($name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                            ]);
                        }
                    }
                }
            });

        $this->printResult('orders.destination_harbor', $count);
    }

    private function backfillOrderDelegationHeaders(): void
    {
        $this->line('→ order_delegation_headers.company_header_name ...');
        $count = 0;

        DB::table('order_delegation_headers')
            ->whereNotNull('company_header_id')
            ->where(function ($q) {
                $q->whereNull('company_header_name')
                    ->orWhere('company_header_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('company_headers')->where('id', $row->company_header_id)->value('company_name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('order_delegation_headers')->where('id', $row->id)->update(['company_header_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('order_delegation_headers.company_header_name', $count);
    }

    private function backfillContainerTypes(): void
    {
        $this->line('→ containers.container_type_name ...');
        $count = 0;

        DB::table('containers')
            ->whereNotNull('container_type_id')
            ->where(function ($q) {
                $q->whereNull('container_type_name')
                    ->orWhere('container_type_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('container_types')->where('id', $row->container_type_id)->value('name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('containers')->where('id', $row->id)->update(['container_type_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('containers.container_type_name', $count);
    }

    private function backfillContainerFleets(): void
    {
        $this->line('→ containers.fleet_name ...');
        $count = 0;

        DB::table('containers')
            ->whereNotNull('fleet_id')
            ->where(function ($q) {
                $q->whereNull('fleet_name')
                    ->orWhere('fleet_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('fleets')->where('id', $row->fleet_id)->value('name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('containers')->where('id', $row->id)->update(['fleet_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('containers.fleet_name', $count);
    }

    private function backfillContainerWharves(): void
    {
        $wharfFields = [
            'pre_pull_wharf_id' => ['nameField' => 'pre_pull_wharf_name', 'table' => 'yard_wharves'],
            'wharf_id'          => ['nameField' => 'wharf_name', 'table' => 'wharves'],
            'drop_off_wharf_id' => ['nameField' => 'drop_off_wharf_name', 'table' => 'yard_wharves'],
        ];

        foreach ($wharfFields as $idField => $config) {
            $nameField = $config['nameField'];
            $lookupTable = $config['table'];
            $this->line("→ containers.{$nameField} ...");
            $count = 0;

            DB::table('containers')
                ->whereNotNull($idField)
                ->where(function ($q) use ($nameField) {
                    $q->whereNull($nameField)
                        ->orWhere($nameField, '');
                })
                ->orderBy('id')
                ->chunk(200, function ($rows) use (&$count, $idField, $nameField, $lookupTable) {
                    foreach ($rows as $row) {
                        $name = DB::table($lookupTable)->where('id', $row->{$idField})->value('name');
                        if ($name !== null) {
                            $count++;
                            if (!$this->dryRun) {
                                DB::table('containers')->where('id', $row->id)->update([$nameField => $name]);
                            }
                        }
                    }
                });

            $this->printResult("containers.{$nameField}", $count);
        }
    }

    private function backfillContainerLoadingAddresses(): void
    {
        $this->line('→ container_loading_addresses.loading_address ...');
        $count = 0;

        DB::table('container_loading_addresses')
            ->whereNotNull('loading_address_id')
            ->where(function ($q) {
                $q->whereNull('loading_address')
                    ->orWhere('loading_address', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $address = DB::table('loading_addresses')->where('id', $row->loading_address_id)->value('address');
                    if ($address !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('container_loading_addresses')->where('id', $row->id)->update(['loading_address' => $address]);
                        }
                    }
                }
            });

        $this->printResult('container_loading_addresses.loading_address', $count);
    }

    private function backfillOrderPaymentsCompanyHeader(): void
    {
        $this->line('→ order_payments.company_header_name ...');
        $count = 0;

        DB::table('order_payments')
            ->whereNotNull('company_header_id')
            ->where(function ($q) {
                $q->whereNull('company_header_name')
                    ->orWhere('company_header_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('company_headers')->where('id', $row->company_header_id)->value('company_name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('order_payments')->where('id', $row->id)->update(['company_header_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('order_payments.company_header_name', $count);
    }

    private function backfillOrderReceiptsCompanyHeader(): void
    {
        $this->line('→ order_receipts.company_header_name ...');
        $count = 0;

        DB::table('order_receipts')
            ->whereNotNull('company_header_id')
            ->where(function ($q) {
                $q->whereNull('company_header_name')
                    ->orWhere('company_header_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('company_headers')->where('id', $row->company_header_id)->value('company_name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('order_receipts')->where('id', $row->id)->update(['company_header_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('order_receipts.company_header_name', $count);
    }

    private function backfillInvoicesInvoiceType(): void
    {
        $this->line('→ invoices.invoice_type_name ...');
        $count = 0;

        DB::table('invoices')
            ->whereNotNull('invoice_type_id')
            ->where(function ($q) {
                $q->whereNull('invoice_type_name')
                    ->orWhere('invoice_type_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('invoice_types')->where('id', $row->invoice_type_id)->value('name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('invoices')->where('id', $row->id)->update(['invoice_type_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('invoices.invoice_type_name', $count);
    }

    private function backfillInvoiceItemsFeeType(): void
    {
        $this->line('→ invoice_items.fee_type_name ...');
        $count = 0;

        DB::table('invoice_items')
            ->whereNotNull('fee_type_id')
            ->where(function ($q) {
                $q->whereNull('fee_type_name')
                    ->orWhere('fee_type_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('fee_types')->where('id', $row->fee_type_id)->value('name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('invoice_items')->where('id', $row->id)->update(['fee_type_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('invoice_items.fee_type_name', $count);
    }

    private function backfillOrderBillItemsFeeType(): void
    {
        $this->line('→ order_bill_items.fee_type_name ...');
        $count = 0;

        DB::table('order_bill_items')
            ->whereNotNull('fee_type_id')
            ->where(function ($q) {
                $q->whereNull('fee_type_name')
                    ->orWhere('fee_type_name', '');
            })
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = DB::table('fee_types')->where('id', $row->fee_type_id)->value('name');
                    if ($name !== null) {
                        $count++;
                        if (!$this->dryRun) {
                            DB::table('order_bill_items')->where('id', $row->id)->update(['fee_type_name' => $name]);
                        }
                    }
                }
            });

        $this->printResult('order_bill_items.fee_type_name', $count);
    }

    private function printResult(string $table, int $count): void
    {
        $this->totalUpdated += $count;
        $action = $this->dryRun ? '待更新' : '已更新';
        if ($count > 0) {
            $this->info("  ✓ {$table}：{$action} {$count} 条");
        } else {
            $this->line("  - {$table}：无需更新");
        }
    }
}
