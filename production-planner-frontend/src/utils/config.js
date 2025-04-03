export const API_BASE_URL = "http://localhost/api";
export const API_BASE_AUTH_URL = `${API_BASE_URL}/auth`;

export const API_ENDPOINTS = {
  getProducibleItems: `${API_BASE_URL}/producible-items`,
  getItem: (itemId) => `${API_BASE_URL}/items/${itemId}`,
  getRecipeForItem: (itemId) => `${API_ENDPOINTS.getItem(itemId)}/standard-recipe`,
  getMachine: (machineId) => `${API_BASE_URL}/machines/${machineId}`,
  getProdPlans: (userId) => `${API_BASE_URL}/users/${userId}/plans`,
  getProdPlan: (planId) => `${API_BASE_URL}/plans/${planId}`,
  createProdPlan: `${API_BASE_URL}/plans`,
  updateProdPlan: (planId) => `${API_BASE_URL}/plans/${planId}`,
  deleteProdPlan: (planId) => `${API_BASE_URL}/plans/${planId}`,
  login: `${API_BASE_AUTH_URL}/login`,
  register: `${API_BASE_AUTH_URL}/register`,
  me: `${API_BASE_AUTH_URL}/me`
};