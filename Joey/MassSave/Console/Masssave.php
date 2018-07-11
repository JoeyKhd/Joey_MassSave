<?php

namespace Joey\MassSave\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Masssave extends Command {

	public function __construct(
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\App\State $area,
		\Magento\Catalog\Model\ProductRepository $product

	) {
		parent::__construct();
		$this->productFactory = $productFactory;
		$this->area           = $area;
		$this->product        = $product;
	}


	protected function configure() {
		$this->setName( 'masssave:save' );
		$this->setDescription( 'Re-saves all available products.' );

		parent::configure();
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {

		$helper = $this->getHelper('question');
		$question = new Question("From which product id would you like to start saving? (ex. id 1000) \n", 0);

		$offset = $helper->ask($input, $output, $question);

		$start = $offset;

		$collection = $this->productFactory->create()->getCollection();
		$this->area->setAreaCode( \Magento\Framework\App\Area::AREA_ADMINHTML );

		$totalproducts = $collection->count();
			
		$output->writeln( 'Re-saving products, this might take a while..' );

		for ( $x = $start; $x < $totalproducts; $x ++ ) {
			$now = $this->product->getById( $x );
			$now->save();
		}

	}
}
