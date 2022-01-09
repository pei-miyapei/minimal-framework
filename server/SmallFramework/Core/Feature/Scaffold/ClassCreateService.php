<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Scaffold;

class ClassCreateService
{
    public static function make(string $nameSpace, string $comment, string $className, string $content): string
    {
        return sprintf(
            implode("\n", [
                '<?php',
                '%s',
                '/**',
                ' * %s',
                ' */',
                'class %s',
                '{',
                '%s',
                '}',
            ])."\n\n",
            \strlen($nameSpace) < 1 ? '' : sprintf("namespace %s;\n", $nameSpace),
            $comment,
            $className,
            $content
        );
    }
}
