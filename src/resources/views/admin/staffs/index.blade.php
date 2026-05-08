@extends('layouts.app')

@section('title', 'スタッフ一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staffs/index.css') }}">
@endsection

@section('content')
    <div class="staff-list-container">
        <h2 class="staff-list-title">スタッフ一覧</h2>

        <table class="staff-list-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($staffs as $staff)
                    <tr>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>
                            <a href="#" class="detail-link"></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">スタッフが登録されていません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection