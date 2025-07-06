<?PHP

declare(strict_types=1);

interface ViewInterface
{
  public function render(string $file, array $data): mixed;
}