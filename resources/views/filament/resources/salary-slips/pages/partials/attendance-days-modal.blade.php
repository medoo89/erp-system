<div>
    <style>
        .sf-attendance-table {
            width: 100%;
            border: 1px solid #e5edf0;
            border-radius: 20px;
            overflow: hidden;
            background: white;
        }

        .sf-attendance-row {
            display: grid;
            grid-template-columns: 130px 150px 90px 220px minmax(260px, 1fr);
            gap: 14px;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f4;
        }

        .sf-attendance-row:last-child {
            border-bottom: 0;
        }

        .sf-attendance-head {
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sf-attendance-input,
        .sf-attendance-select {
            width: 100%;
            height: 42px;
            border-radius: 14px;
            border: 1px solid #dbe4e8;
            padding: 0 12px;
            font-weight: 650;
            outline: none;
        }

        .sf-attendance-check {
            width: 22px;
            height: 22px;
            accent-color: #2563eb;
        }

        .sf-attendance-text {
            font-weight: 750;
            color: #0f172a;
        }
    </style>

    <div class="sf-attendance-table">
        <div class="sf-attendance-row sf-attendance-head">
            <div>Date</div>
            <div>Day</div>
            <div>Paid</div>
            <div>Status</div>
            <div>Notes</div>
        </div>

        @foreach($this->attendanceRows as $index => $row)
            <div class="sf-attendance-row" wire:key="attendance-row-{{ $row['id'] ?? $index }}">
                <div class="sf-attendance-text">{{ $row['date'] ?? '-' }}</div>
                <div class="sf-attendance-text">{{ $row['day_name'] ?? '-' }}</div>

                <div>
                    <input
                        type="checkbox"
                        class="sf-attendance-check"
                        wire:model="attendanceRows.{{ $index }}.is_paid"
                    >
                </div>

                <div>
                    <select
                        class="sf-attendance-select"
                        wire:model="attendanceRows.{{ $index }}.status"
                    >
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="sick">Sick</option>
                        <option value="leave">Leave</option>
                        <option value="unpaid_leave">Unpaid Leave</option>
                        <option value="holiday">Holiday</option>
                        <option value="travel">Travel</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <input
                        type="text"
                        class="sf-attendance-input"
                        wire:model="attendanceRows.{{ $index }}.notes"
                        placeholder="Optional notes..."
                    >
                </div>
            </div>
        @endforeach
    </div>
</div>
