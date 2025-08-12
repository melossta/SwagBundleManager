<?php declare(strict_types=1);

namespace SwagBundleManager\Command;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTestBundleCommand extends Command
{

    private $bundleRepository;

    public function __construct(EntityRepository $bundleRepository
    ) {
        parent::__construct();
        $this->bundleRepository = $bundleRepository;
    }

    protected function configure(): void
    {
        $this->setName('swag:bundle:create-test')->setDescription('Creates a test bundle with products');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createDefaultContext();

        // Lowercase product IDs (32 hex characters)
        $productId1 = '01985125dd1d756ab673145d0d7e13da';
        $productId2 = '0198512678c276808dd58fbb8a6fbfc3';
        $productId3 = '01985ab4dd097d90ad8ab0f4e6a8c919';

        $bundleId = Uuid::randomHex();

        // Use the language ID you provided (lowercase)
        $languageId = '2fbb5fe2e29a4d70aa5854ce7ce3e20b';

        $data = [
            'id' => $bundleId,
            'discountType' => 'percent',
            'discount' => 15.0,
            'translations' => [
                $languageId => ['name' => 'My Test Bundle']
            ],
            'products' => [
                ['id' => $productId1],
                ['id' => $productId2],
                ['id' => $productId3],
            ],
        ];

        try {
            $this->bundleRepository->create([$data], $context);
            $output->writeln('Bundle created with ID: ' . $bundleId);
        } catch (\Exception $e) {
            $output->writeln('Error creating bundle: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}
