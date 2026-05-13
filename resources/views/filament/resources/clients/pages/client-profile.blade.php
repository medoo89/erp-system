<x-filament-panels::page>
    <div style="display:flex; flex-direction:column; gap:24px;">
        <div style="border-radius:28px; padding:28px; background:linear-gradient(135deg, #eff6ff 0%, #f8fbff 42%, #ecfeff 100%); border:1px solid #dbeafe; box-shadow:0 20px 60px rgba(15, 23, 42, 0.06);">
            <div style="display:flex; justify-content:space-between; gap:20px; align-items:flex-start; flex-wrap:wrap;">
                <div>
                    <div style="font-size:42px; line-height:1.02; font-weight:900; color:#234b7b; letter-spacing:-0.03em;">
                        {{ $client->name ?? 'Client' }}
                    </div>
                    <div style="margin-top:10px; color:#64748b; font-size:16px; max-width:860px;">
                        Client master profile with direct access to related projects and business information.
                    </div>
                </div>

                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <div style="padding:10px 14px; background:#ffffff; border:1px solid #dbeafe; border-radius:999px; color:#1d4ed8; font-weight:800; font-size:13px;">
                        Code: {{ $client->code ?: '-' }}
                    </div>

                    <div style="padding:10px 14px; background:#ffffff; border:1px solid #dbeafe; border-radius:999px; color:{{ $client->is_active ? '#15803d' : '#64748b' }}; font-weight:800; font-size:13px;">
                        {{ $client->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:20px;">
            <div style="background:#ffffff; border:1px solid #dbe4ee; border-radius:24px; padding:24px; box-shadow:0 14px 36px rgba(15, 23, 42, 0.04);">
                <div style="font-size:22px; font-weight:800; color:#0f172a; margin-bottom:18px;">Client Information</div>

                <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px;">
                    <div>
                        <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Client Name</div>
                        <div style="font-size:16px; font-weight:700; color:#0f172a;">{{ $client->name ?: '-' }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Client Code</div>
                        <div style="font-size:16px; font-weight:700; color:#0f172a;">{{ $client->code ?: '-' }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Contact Person</div>
                        <div style="font-size:16px; font-weight:700; color:#0f172a;">{{ $client->contact_person ?: '-' }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Email</div>
                        <div style="font-size:16px; font-weight:700; color:#0f172a;">{{ $client->email ?: '-' }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Phone</div>
                        <div style="font-size:16px; font-weight:700; color:#0f172a;">{{ $client->phone ?: '-' }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Status</div>
                        <div style="font-size:16px; font-weight:700; color:{{ $client->is_active ? '#15803d' : '#64748b' }};">
                            {{ $client->is_active ? 'Active' : 'Inactive' }}
                        </div>
                    </div>
                </div>
            </div>

            <div style="background:#ffffff; border:1px solid #dbe4ee; border-radius:24px; padding:24px; box-shadow:0 14px 36px rgba(15, 23, 42, 0.04);">
                <div style="font-size:22px; font-weight:800; color:#0f172a; margin-bottom:18px;">Additional Information</div>

                <div style="display:flex; flex-direction:column; gap:18px;">
                    <div>
                        <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Address</div>
                        <div style="font-size:15px; font-weight:600; color:#0f172a;">{{ $client->address ?: '-' }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Notes</div>
                        <div style="font-size:15px; font-weight:600; color:#0f172a;">{{ $client->notes ?: '-' }}</div>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px;">
                        <div>
                            <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Created At</div>
                            <div style="font-size:15px; font-weight:600; color:#0f172a;">{{ optional($client->created_at)->format('M j, Y H:i') ?: '-' }}</div>
                        </div>

                        <div>
                            <div style="font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:6px;">Last Updated</div>
                            <div style="font-size:15px; font-weight:600; color:#0f172a;">{{ optional($client->updated_at)->format('M j, Y H:i') ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid #dbe4ee; border-radius:24px; padding:24px; box-shadow:0 14px 36px rgba(15, 23, 42, 0.04);">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:18px;">
                <div>
                    <div style="font-size:22px; font-weight:800; color:#0f172a;">Projects</div>
                    <div style="margin-top:6px; color:#64748b; font-size:14px;">Projects linked to this client.</div>
                </div>
            </div>

            <div style="overflow:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc; color:#7c8aa0; text-transform:uppercase; font-size:13px;">
                            <th style="text-align:left; padding:14px;">Project</th>
                            <th style="text-align:left; padding:14px;">Project Code</th>
                            <th style="text-align:left; padding:14px;">Location</th>
                            <th style="text-align:left; padding:14px;">Jobs</th>
                            <th style="text-align:left; padding:14px;">Status</th>
                            <th style="text-align:left; padding:14px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr style="border-top:1px solid #e5edf5;">
                                <td style="padding:16px 14px; font-weight:800; color:#0f172a;">{{ $project->name ?: '-' }}</td>
                                <td style="padding:16px 14px; color:#0f172a;">{{ $project->project_code ?: '-' }}</td>
                                <td style="padding:16px 14px; color:#0f172a;">{{ $project->location ?: '-' }}</td>
                                <td style="padding:16px 14px; color:#0f172a;">{{ $project->jobs_count ?? 0 }}</td>
                                <td style="padding:16px 14px; color:{{ $project->is_active ? '#15803d' : '#64748b' }}; font-weight:700;">
                                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                                </td>
                                <td style="padding:16px 14px;">
                                    <a
                                        href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('edit', ['record' => $project]) }}"
                                        style="display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-weight:800; font-size:12px; text-decoration:none;"
                                    >
                                        Open Project
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:18px 14px; color:#94a3b8;">No projects linked to this client yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
