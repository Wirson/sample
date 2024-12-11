<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Console\Command;

class ProductUpdate extends \Symfony\Component\Console\Command\Command
{
    public function __construct(
        private readonly \Wira\AjaxPrice\Cron\UpdateProduct $updateProduct,
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('wira:productupdate');
        $this->setDescription('Update Product Data from BusinessEdge API');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $output->setDecorated(true);
        $this->updateProduct->execute();
        $output->writeln('<info>Product updated successfully!</info>');
        return 1;
    }
}
