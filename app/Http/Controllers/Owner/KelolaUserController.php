<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class KelolaUserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        return view('owner.kelola-user');
    }

    /**
     * Get users data for DataTable (AJAX)
     */
    public function getData(Request $request)
    {
        try {
            $query = User::with('role');

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhereHas('role', function($roleQuery) use ($search) {
                          $roleQuery->where('nama_role', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by role
            if ($request->filled('role_filter')) {
                $query->where('role_id', $request->role_filter);
            }

            // Filter by status
            if ($request->filled('status_filter')) {
                $status = $request->status_filter === 'active' ? true : false;
                $query->where('is_active', $status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            // Format data
            $formattedData = $users->getCollection()->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '-',
                    'address' => $user->address ?? '-',
                    'role_name' => $user->role_name,
                    'role_id' => $user->role_id,
                    'is_active' => $user->is_active,
                    'status_text' => $user->is_active ? 'Active' : 'Inactive',
                    'status_badge_class' => $user->is_active ? 
                        'px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full' :
                        'px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full',
                    'role_badge_class' => $this->getRoleBadgeClass($user->role_name),
                    'initials' => $user->initials,
                    'created_at' => $user->created_at->format('d/m/Y H:i'),
                    'created_at_diff' => $user->created_at->diffForHumans()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        try {
            \Log::info('=== DEBUG USER CREATION ===');
            \Log::info('Request data: ', $request->all());
            
            // ✅ CEK SATU-SATU VALIDATION RULES
            
            // 1. Cek email unique
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                \Log::error('Email already exists: ' . $request->email);
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah digunakan',
                    'errors' => ['email' => ['Email sudah terdaftar']]
                ], 422);
            }
            
            // 2. Cek role exists
            $role = Role::find($request->role_id);
            if (!$role) {
                \Log::error('Role not found: ' . $request->role_id);
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak ditemukan',
                    'errors' => ['role_id' => ['Role tidak valid']]
                ], 422);
            }
            
            // 3. Manual validation dengan rules yang lebih permisif
            $validator = \Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', 'min:5'], // ✅ GANTI KE MIN 5 DULU
                'role_id' => ['required', 'exists:roles,id'],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:500'],
                'is_active' => ['required', 'boolean']
            ]);
            
            if ($validator->fails()) {
                \Log::error('Validation failed: ', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'debug_request' => $request->all()
                ], 422);
            }

            // 4. Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => $request->is_active
            ]);

            \Log::info('User created successfully: ', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception in store method: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'debug_request' => $request->all()
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'role_id' => $user->role_id,
                    'role_name' => $user->role_name,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at->format('d/m/Y H:i'),
                    'updated_at' => $user->updated_at->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
                'role_id' => ['required', 'exists:roles,id'],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:500'],
                'is_active' => ['required', 'boolean']
            ];

            // Only validate password if it's provided
            if ($request->filled('password')) {
                $rules['password'] = ['confirmed', Rules\Password::defaults()];
            }

            $request->validate($rules);

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => $request->is_active
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => $user
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting own account
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun sendiri'
                ], 400);
            }

            // Check if user has related data
            $hasTransactions = $user->transaksi()->count() > 0;
            $hasRestockRequests = $user->restockRequestGudang()->count() > 0;
            $hasBarangMasuk = $user->barangMasuk()->count() > 0;

            if ($hasTransactions || $hasRestockRequests || $hasBarangMasuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak dapat dihapus karena memiliki data transaksi/aktivitas terkait'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deactivating own account
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menonaktifkan akun sendiri'
                ], 400);
            }

            $user->update(['is_active' => !$user->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Status user berhasil diubah',
                'new_status' => $user->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status user'
            ], 500);
        }
    }

    /**
     * Get roles for dropdown
     */
    public function getRoles()
    {
        try {
            $roles = Role::orderBy('nama_role')->get()->map(function($role) {
                return [
                    'id' => $role->id,
                    'nama_role' => $role->nama_role
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $roles
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data roles'
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'inactive_users' => User::where('is_active', false)->count(),
                'users_by_role' => User::join('roles', 'users.role_id', '=', 'roles.id')
                    ->groupBy('roles.nama_role')
                    ->selectRaw('roles.nama_role, count(*) as count')
                    ->pluck('count', 'nama_role')
                    ->toArray()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik'
            ], 500);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        try {
            $request->validate([
                'new_password' => ['required', 'confirmed', Rules\Password::defaults()]
            ]);

            $user = User::findOrFail($id);
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat reset password'
            ], 500);
        }
    }

    /**
     * Get role badge CSS class
     */
    private function getRoleBadgeClass($roleName)
    {
        return match($roleName) {
            'Owner' => 'px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full',
            'Gudang' => 'px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full',
            'Kasir' => 'px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded-full',
            default => 'px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full'
        };
    }
}