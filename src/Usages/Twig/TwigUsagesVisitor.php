<?php declare(strict_types=1);

namespace Becklyn\GluggiBundle\Usages\Twig;

use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

class TwigUsagesVisitor implements NodeVisitorInterface
{
    /**
     * @var string[][]
     */
    private $usages = [];

    /**
     *
     */
    public function reset () : void
    {
        $this->usages = [];
    }


    /**
     */
    public function getUsages () : array
    {
        return $this->usages;
    }


    /**
     * @inheritDoc
     */
    public function enterNode (Node $node, Environment $env) : Node
    {
        if (!$node instanceof FunctionExpression)
        {
            // abort: not function call
            return $node;
        }

        $name = $node->getAttribute("name");

        if ("gluggi" !== $name && "gluggi_template" !== $name)
        {
            // abort: function call not to gluggi
            return $node;
        }

        $arguments = $node->getNode("arguments");

        if (2 > \count($arguments))
        {
            // abort: not enough arguments
            return $node;
        }

        $typeArgument = $arguments->getNode("0");
        $nameArgument = $arguments->getNode("1");

        // everything correct -> store
        if ($typeArgument instanceof ConstantExpression && $nameArgument instanceof ConstantExpression)
        {
            $this->usages[$typeArgument->getAttribute("value")][] = $nameArgument->getAttribute("value");
        }

        return $node;
    }


    /**
     * @inheritDoc
     */
    public function leaveNode (Node $node, Environment $env) : Node
    {
        return $node;
    }


    /**
     * @inheritDoc
     */
    public function getPriority () : int
    {
        return 0;
    }
}
