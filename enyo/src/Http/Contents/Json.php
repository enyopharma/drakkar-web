<?php declare(strict_types=1);

namespace Enyo\Http\Contents;

final class Json implements \JsonSerializable
{
    private $data;

    private $status;

    private $reason;

    public function __construct(array $data, int $status = 200, string $reason = '')
    {
        $this->data = $data;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function jsonSerialize()
    {
        return [
            'success' => $this->status == 200,
            'status' => $this->status,
            'reason' => $this->reason,
            'data' => $this->data,
        ];
    }
}
