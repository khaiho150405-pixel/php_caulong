
@if ($items->hasPages())
    <div class="mt-3">
        {{ $items->onEachSide(1)->links() }}
    </div>
@elseif ($items->total() > 0)
    <div class="mt-3 small text-muted">Tổng cộng {{ $items->total() }} kết quả</div>
@endif
