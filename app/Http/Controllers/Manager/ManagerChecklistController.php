<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CompanyChecklist;
use App\Models\ChecklistCategory;
use App\Models\ChecklistItem;
use App\Models\ContractedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerChecklistController extends Controller
{
    private function getContractedClient()
    {
        return ContractedClient::where('user_id', Auth::user()->id)->first();
    }

    /**
     * Display the checklist management page.
     */
    public function index()
    {
        $contractedClient = $this->getContractedClient();

        $checklist = null;
        $checklistData = null;

        if ($contractedClient) {
            $checklist = CompanyChecklist::where('contracted_client_id', $contractedClient->id)
                ->where('is_active', true)
                ->with(['categories.items'])
                ->first();

            if ($checklist) {
                $checklistData = [
                    'id' => $checklist->id,
                    'name' => $checklist->name,
                    'important_reminders' => $checklist->important_reminders,
                    'categories' => $checklist->categories->map(function ($c) {
                        return [
                            'id' => $c->id,
                            'name' => $c->name,
                            'items' => $c->items->map(function ($i) {
                                return [
                                    'id' => $i->id,
                                    'name' => $i->name,
                                    'quantity' => $i->quantity,
                                ];
                            })->values()->toArray(),
                        ];
                    })->values()->toArray(),
                ];
            }
        }

        $predefinedCategories = [
            'Kitchen', 'Bathroom', 'Lavatory', 'Living Room', 'Bedroom',
            'Dining Room', 'Outdoor', 'Garage', 'Office', 'Laundry',
            'Storage', 'Chimney', 'Condiments', 'Supplies'
        ];

        return view('manager.checklist', compact('checklist', 'checklistData', 'predefinedCategories'));
    }

    /**
     * Create a new checklist (AJAX).
     */
    public function store(Request $request)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'important_reminders' => 'nullable|string',
        ]);

        $checklist = CompanyChecklist::create([
            'contracted_client_id' => $contractedClient->id,
            'name' => $request->name,
            'important_reminders' => $request->important_reminders,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Checklist created successfully',
            'checklist' => $checklist->load('categories.items'),
        ], 201);
    }

    /**
     * Delete an entire checklist with its categories and items (AJAX).
     */
    public function destroy($checklistId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $checklist = CompanyChecklist::where('id', $checklistId)
            ->where('contracted_client_id', $contractedClient->id)
            ->firstOrFail();

        $checklist->delete();

        return response()->json(['message' => 'Checklist deleted successfully']);
    }

    /**
     * Update checklist details (AJAX).
     */
    public function update(Request $request, $checklistId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $checklist = CompanyChecklist::where('id', $checklistId)
            ->where('contracted_client_id', $contractedClient->id)
            ->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'important_reminders' => 'nullable|string',
        ]);

        $checklist->update($request->only(['name', 'important_reminders']));

        return response()->json(['message' => 'Checklist updated successfully', 'checklist' => $checklist]);
    }

    /**
     * Add a category to the checklist (AJAX).
     */
    public function addCategory(Request $request, $checklistId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $checklist = CompanyChecklist::where('id', $checklistId)
            ->where('contracted_client_id', $contractedClient->id)
            ->firstOrFail();

        $request->validate(['name' => 'required|string|max:255']);

        $maxSort = $checklist->categories()->max('sort_order') ?? 0;

        $category = $checklist->categories()->create([
            'name' => $request->name,
            'sort_order' => $maxSort + 1,
        ]);

        return response()->json([
            'message' => 'Category added successfully',
            'category' => $category->load('items'),
        ], 201);
    }

    /**
     * Update a category (AJAX).
     */
    public function updateCategory(Request $request, $categoryId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $category = ChecklistCategory::whereHas('checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->findOrFail($categoryId);

        $request->validate(['name' => 'required|string|max:255']);
        $category->update(['name' => $request->name]);

        return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
    }

    /**
     * Delete a category (AJAX).
     */
    public function deleteCategory($categoryId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $category = ChecklistCategory::whereHas('checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->findOrFail($categoryId);

        $category->items()->delete();
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    /**
     * Add an item to a category (AJAX).
     */
    public function addItem(Request $request, $categoryId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $category = ChecklistCategory::whereHas('checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->findOrFail($categoryId);

        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'nullable|string|max:50',
        ]);

        $maxSort = $category->items()->max('sort_order') ?? 0;

        $item = $category->items()->create([
            'name' => $request->name,
            'quantity' => $request->quantity ?? '1',
            'sort_order' => $maxSort + 1,
        ]);

        return response()->json(['message' => 'Item added successfully', 'item' => $item], 201);
    }

    /**
     * Update an item (AJAX).
     */
    public function updateItem(Request $request, $itemId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $item = ChecklistItem::whereHas('category.checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->findOrFail($itemId);

        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'nullable|string|max:50',
        ]);

        $item->update($request->only(['name', 'quantity']));

        return response()->json(['message' => 'Item updated successfully', 'item' => $item]);
    }

    /**
     * Delete an item (AJAX).
     */
    public function deleteItem($itemId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $item = ChecklistItem::whereHas('category.checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->findOrFail($itemId);

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}
