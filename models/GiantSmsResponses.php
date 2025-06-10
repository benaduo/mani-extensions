<?php

namespace ManiExtensions\Models;

use DateTime;

class BaseResponse
{
    protected bool $status;
    protected string $message;

    public function __construct($data)
    {
        $this->status = $data->status ?? false;
        $this->message = $data->message ?? 'An error occurred';
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function toApiResponse()
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
        ];
    }
}

class MessageStatus
{
    protected string $message_id;
    protected DateTime $scheduled_date;
    protected string $rate;
    protected string $status;
    protected string $reason;
    protected DateTime $last_updated_at;

    public function __construct($data)
    {
        $this->message_id = $data->message_id ?? '';
        $this->scheduled_date = isset($data->scheduled_date) ? new DateTime($data->scheduled_date) : new DateTime();
        $this->rate = $data->rate ?? '';
        $this->status = $data->status ?? '';
        $this->reason = $data->reason ?? '';
        $this->last_updated_at = isset($data->last_updated_at) ? new DateTime($data->last_updated_at) : new DateTime();
    }

    public function getMessageId()
    {
        return $this->message_id;
    }

    public function getScheduledDate()
    {
        return $this->scheduled_date;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function getLastUpdatedAt()
    {
        return $this->last_updated_at;
    }

    public function toApiResponse()
    {
        return [
            'message_id' => $this->message_id,
            'schedule_date' => $this->scheduled_date ? $this->scheduled_date->format('Y-m-d H:i') : null,
            'rate' => $this->rate,
            'status' => $this->status,
            'reason' => $this->reason,
            'last_updated_date' => $this->last_updated_at ? $this->last_updated_at->format('Y-m-d H:i') : null,
        ];
    }
}

class SenderIdData
{
    protected string $name;
    protected string $purpose;
    protected bool $approved;
    protected string $approval_status;

    public function __construct($data)
    {
        $this->name = $data->name ?? '';
        $this->purpose = $data->purpose ?? '';
        $this->approved = $data->approved ?? false;
        $this->approval_status = $data->approval_status ?? '';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPurpose()
    {
        return $this->purpose;
    }

    public function isApproved()
    {
        return $this->approved;
    }

    public function getApprovalStatus()
    {
        return $this->approval_status;
    }
    public function toApiResponse()
    {
        return [
            'name' => $this->name,
            'purpose' => $this->purpose,
            'approved' => $this->approved,
            'approval_status' => $this->approval_status,
        ];
    }
}

class SingleSmsResponse extends BaseResponse
{
    protected MessageStatus $data;

    public function __construct($response)
    {
        parent::__construct($response);
        $data = $response->data ?? $response;
        $this->data = new MessageStatus($data);
    }

    public function getMessageStatus()
    {
        return $this->data;
    }

    public function toApiResponse()
    {
        return [
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'data' => $this->data->toApiResponse(),
        ];
    }
}

class SenderIdResponse extends BaseResponse
{
    protected array $data;
    public function __construct($response)
    {
        parent::__construct($response);
        $this->data = array_map(function ($item) {
            return new SenderIdData($item);
        }, $response->data ?? []);
    }

    public function getSenderIds()
    {
        return array_map(function ($senderId) {
            return $senderId->toApiResponse();
        }, $this->data);
    }

    public function toApiResponse()
    {
        return [
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'data' => $this->getSenderIds(),
        ];
    }
}
